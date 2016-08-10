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

$Title = 'Update tournament fees and dates';
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
<h1>Update tournament fee/dates details</h1>
<p>Please enter the required details in the form below.</p>
<p>Use this form to update the fee and date details of the tournament.</p>
<form name="utform" action="updfeedates2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkform();">
 <?php $tourn->set_hidden(); ?>
<table cellpadding="5" cellspacing="5" align="left" width="800"  summary="Tournament Table">
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
<td>Date (or first day)</td>
<td>
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
<tr>
<td>Basic fee for tournament</td>
<?php
print <<<EOT
<td><input type="text" name="fee" value="{$tourn->display_fee()}" size="6" maxlength="6" /></td>

EOT;
?>
</tr>
<tr>
<td>Supplement for lunch, put zero if no lunch</td>
<?php
print <<<EOT
<td><input type="text" name="lunch" value="{$tourn->display_lunch()}" size="6" maxlength="6" /></td>

EOT;
?>
</tr>
<tr>
<td>Dinner or other event, blank if none</td>
<?php
print <<<EOT
<td><input type="text" name="dinner" value="{$tourn->display_dinner()}" size="20" /></td>

EOT;
?>
</tr>
<tr>
<td>Supplement if not BGA member</td>
<?php
print <<<EOT
<td><input type="text" name="nonbga" value="{$tourn->display_nonbga()}" size="6" maxlength="6" /></td>
	
EOT;
?>
</tr>
<tr>
<td colspan="2">The following two fields are concession amounts.
Typically entrants are either standard, or concessionary if retired or students.
This allows up to two alternative concession discounts, plus descriptions to be assigned.
Just set one to zero to turn it off.</td></tr>
<tr><td>Concession 1 amt/descr</td>
<?php
print <<<EOT
<td><input type="text" name="concess1" value="{$tourn->display_concess1()}" size="6" maxlength="6" />
<input type="text" name="concess1name" value="{$tourn->display_concess1name()}" size="20" /></td>

EOT;
?>
</tr><tr><td>Concession 2 amt/descr</td>
<?php
print <<<EOT
<td><input type="text" name="concess2" value="{$tourn->display_concess2()}" size="6" maxlength="6" />
<input type="text" name="concess2name" value="{$tourn->display_concess2name()}" size="20" /></td>

EOT;
?>
</tr><tr><td>Late entry fee</td>
<?php
print <<<EOT
<td><input type="text" name="latefee" value="{$tourn->display_latefee()}" size="6" maxlength="6" />

EOT;
?>
</td></tr><tr><td>Late entry applies if booked</td>
<td><select name="latedays">
<?php
for ($i = 0;  $i <= 20;  $i++)  {
	$lab = "$i days before";
	if  ($i == 0)
		$lab = "On day";
	elseif  ($i == 1)
		$lab = "Previous day";
	if  ($i == $tourn->Latedays)
		print "<option value=\"$i\" selected=\"selected\">$lab</option>\n";
	else
		print "<option value=\"$i\">$lab</option>\n";
}
?>
</select></td></tr>
<tr><td>Early bird discount</td>
<?php
print <<<EOT
<td><input type="text" name="ebird" value="{$tourn->display_ebird()}" size="6" maxlength="6" />

EOT;
?>
</td></tr><tr><td>Last day for early bird</td><td>
<?php $tourn->Ebdate->dateopt("eb"); ?>
</td></tr>
<?php include 'php/sumchallenge.php'; ?>
<tr><td><b>Click to update</b></td><td><input type="submit" value="Update tournament"></td></tr>
</table>
</form>
</div>
</div>
</body>
</html>
