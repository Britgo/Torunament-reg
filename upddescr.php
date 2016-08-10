<?php

//   Copyright 2016 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

include 'php/tcerror.php';
include 'php/session.php';
include 'php/checklogged.php';
include 'php/tdate.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/entrant.php';
include 'php/tournclass.php';
include 'php/opendb.php';

// Check the guy can create tournaments before we go any further

if (!$organ)  {
	$mess = 'Not Tournament Organiser';
	include 'php/wrongentry.php';
	exit(0);
}

if (!isset($_GET['tcode']))  {
	$mess = 'No code';
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_GET['tcode'];

try  {
	opendb();
	$orguser = new Player();
	$orguser->fromid($userid);
}
catch (Tcerror $e) {
	$mess = "Cannot open database " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
try  {
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
}
catch (Tcerror $e) {
	$mess = "Cannot load tournament " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$Title = 'Update tournament description';
include 'php/head.php';

?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">

function checkform() {
	var fm = document.utform;
  	if  (!nonblank(fm.tname.value))  {
		alert("Please give a tournament name");
		return false;
	}
   if (!nonblank(fm.address.value)) {
		alert("Please give a venue");
		return false;
   }
   if (!nonblank(fm.postcode.value))  {
		alert("Please give a postcode");
		return  false;
   }
   if (!nonblank(fm.contact.value)) {
   	alert("Please give contact name");
   	return  false;
   }
   if (!nonblank(fm.email.value)) {
   	alert("Please give contact email");
   	return  false;
   }
   try {
    	var tdat = datecheck(fm.year, fm.month, fm.day, "Start date");
   	if (tdat.getDay() > 0  &&  tdat.getDay() < 5  &&  !confirm("Not a weekend - OK"))
			return false;
	}
	catch (err)  {
		alert(err.message);
		return false;
	}
   return true;
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Update tournament address details</h1>
<p>Please enter the required details in the form below.</p>
<p>Use this form to update the vanue and format details of the tournament.</p>
<form name="utform" action="upddescr2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkform();">
<table cellpadding="5" cellspacing="5" align="left" width="800"  summary="Tournament Table">
<tr>
	<td colspan="2">Please give a full name for the tournament, usually just location and year.<br />
	This is normally displayed as a heading in various places.</td>
</tr>
<tr>
	<td><b>Tournament Name</b></td>
<?php
$tourn->set_hidden();
print <<<EOT
<td><input type="text" name="tname" value="{$tourn->display_name()}" size="40"></td>

EOT;
?>
</tr>
<tr>
	<td colspan="2">This is the class of the tournament, A B or C. If this is not a class which
	affects ratings, select N.</td>
</tr>
<tr>
	<td><b>Tournament Class</b></td>
	<td>
<?php
	$ach = $bch = $cch = $nch = "";
	switch ($tourn->Tclass) {
	case 'A':
		$ach = ' checked="checked"';
		break;
	case 'B':
		$bch = ' checked="checked"';
		break;
	case 'C':
		$cch = ' checked="checked"';
		break;
	default:
		$nch = ' checked="checked"';
		break;
	}
print <<<EOT
<input type="radio" name="tclass" value="A"$ach />A
<input type="radio" name="tclass" value="B"$bch />B
<input type="radio" name="tclass" value="C"$cch />C
<input type="radio" name="tclass" value="N"$nch />N

EOT;
?>
</td></tr>
<tr>
	<td><b>Format</b><br />Please give a brief description of the tournament parameters with time limits.
	Say what the rules are if not AGA and 7.5 Komi.</td>
<?php
print <<<EOT
<td><textarea name="format" rows="6" cols="50">{$tourn->display_format()}</textarea></td>

EOT;
?>
</tr>
<tr>
	<td><b>Overview</b><br />Please give a brief description of the tournament timetable. Mention any side
	tournaments e.g. 13x13, quiz etc.</td>
<?php
print <<<EOT
<td><textarea name="overview" rows="6" cols="50">{$tourn->display_over()}</textarea></td>

EOT;
?>
</tr>
<tr>
	<td><b>Venue</b><br />Please give a description of the venue, with street address.</td>
<?php
print <<<EOT
<td><textarea name="address" rows="6" cols="50">{$tourn->display_addr()}</textarea></td>

EOT;
?>
</tr>
<tr>
	<td colspan="2">Please give the correct postcode of the address for the benefit of Sat Nav users.</td>
</tr>
<tr>
	<td><b>Postcode</b></td>
<?php
print <<<EOT
<td><input type="text" name="postcode" size="10" value="{$tourn->display_pc()}"></td>

EOT;
?>
</tr>
<tr>
	<td>Provisional dates and details</td>
<?php
if ($tourn->Provisional)
	print <<<EOT
<td><input type="checkbox" name="provisional" checked="checked"></td>

EOT;
else
	print <<<EOT
<td><input type="checkbox" name="provisional"></td>

EOT;
?>
</tr>
<tr>
	<td>Open for entries</td>
<?php
if ($tourn->Open)
	print <<<EOT
<td><input type="checkbox" name="open" checked="checked"></td>

EOT;
else
	print <<<EOT
<td><input type="checkbox" name="open"></td>

EOT;
?>
</tr>
	<td>Date (or first day)</td><td>
<?php
$tourn->Sdate->dateopt();
?>
</td></tr>
<tr>
	<td>Number of days</td>
	<td><select name="ndays">
<?php
for ($i = 1;  $i <= 7;  $i++)
	if  ($i == $tourn->Ndays)
		print "<option selected=\"selected\">$i</option>\n";
	else
		print "<option>$i</option>\n";
?>
</select></td></tr>
<tr>
	<td>Total number of rounds</td>
	<td><select name="rounds">
<?php
for ($i = 3;  $i <= 20;  $i++)
		if  ($i == $tourn->Nrounds)
			print "<option selected=\"selected\">$i</option>\n";
		else
			print "<option>$i</option>\n";
?>
</select></td></tr>
<?php
if ($admin) {
	print <<<EOT
<tr>
	<td><b>Organiser userid</b></td>
	<td><input type="text" name="organiser" value="$userid" size="16"></td>
</tr>

EOT;
}
else
	$tourn->hidden_organiser();
?>
<tr>
	<td><b>Contact</b></td>
	<td>
<?php
print <<<EOT
<input type="text" name="contact" value="{$tourn->display_contact()}" size="30">

EOT;
?>
</td></tr>
<tr>
	<td><b>Contact email</b></td>
	<td>
<?php
print <<<EOT
<input type="text" name="email" value="{$tourn->display_email()}" size="30"></td></tr>

EOT;
?>
<tr>
	<td><b>Website</b></td><td>
<?php
print <<<EOT
<input type="text" name="website" value="{$tourn->display_ws()}" size="40"></td></tr>

EOT;
?>
</td></tr>
<?php include 'php/sumchallenge.php'; ?>
<tr>
	<td><b>Click to update</b></td>
	<td><input type="submit" value="Update tournament"></td>
</tr>
</table>
</form>
</div>
</div>
</body>
</html>
