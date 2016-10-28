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
include 'php/club.php';
include 'php/country.php';
include 'php/tdate.php';
include 'php/person.php';
include 'php/entrant.php';
include 'php/tournclass.php';
include 'php/opendb.php';

function has_code($code)  {
	$qcode = mysql_real_escape_string($code);
	$ret = mysql_query("select count(*) from tdetails where tcode='$qcode'");
	if ($ret)
		return false;
	$row = mysql_fetch_array($ret);
	return $row[0] > 0;
}

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
}
catch (Tcerror $e) {
	$mess = "Cannot open database " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
try  {
	$tourn = new Tournament($tcode);
	$ptourn = new Tournament($tcode);
	$ptourn->fetchdets();
	$tourn->clonefrom($ptourn);	
}
catch (Tcerror $e) {
	$mess = "Cannot clone tournament " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$name = $tourn->Name;
if (preg_match('/(\d+)/', $tcode, $matches)) {
	$upd = $matches[1] + 1;
	$tcode = preg_replace('/(\d+)/', $upd, $tcode);
}

$ncode = $tcode;
while (has_code($ncode))  {
	$suff++;
	$ncode = $tcode . '_' . $suff;
}

$tcode = $ncode;

if (preg_match('/(\d+)/', $name, $matches)) {
	$upd = $matches[1] + 1;
	$name = preg_replace('/(\d+)/', $upd, $name);
}

$tourn->Tcode = $tcode;
$tourn->Name = $name;
$tourn->Orguser = $userid;
$tourn->Sdate->incdays(60);
$tourn->Ebdate = new Tdate($tourn->Sdate);
$tourn->Ebdate->incdays($ptourn->Sdate->daysbetween($ptourn->Ebdate));

$Title = 'Clone a tournament';
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
<?php
//  Set up list of existing codes to check against
	
print "Existing_codes = new Array();\n";
$codes = Tournament::get_tcodes();
foreach ($codes as $code)
	print "Existing_codes['$code'] = 1;\n";
?>

function checkform() {
	var fm = document.ctform;
 	var tcode = fm.tcode.value;
  	if  (!nonblank(tcode))  {
 	 	alert("Please give a tournament code (e.g. anytown2014)");
     	return false;
   }
	if  (!okcode(tcode))  {
		alert("Please give a meaningful tournament code letters/digits");
		return false;
	}
	if (Existing_codes[tcode]) {
     	alert("Already have a tournament called " + tcode);
     	return false;
   }
  	if  (!nonblank(fm.tname.value))  {
		alert("Please give a tournament name");
		return false;
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
<h1>Clone a tournament</h1>
<?php
print <<<EOT
<p>This is cloned from the {$ptourn->display_name()} tournament.</p>

EOT;
?>
<p>Please just adjust the code, name and start date and then use other links to correct and adjust other fields.</p>
<form name="ctform" action="clonet2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkform();">
<table cellpadding="5" cellspacing="5" align="left" width="800"  summary="Tournament Table">
<tr>
	<td colspan="2">Please enter a unique single-word code for the tournament here.<br />This should be letters and digits only.</td>
</tr>
<tr>
	<td><b>Tournament Code</b></td>
<?php
$ptourn->set_hidden('p');
print <<<EOT
	<td><input type="text" name="tcode" size="20" value="{$tourn->display_code()}"></td>
</tr>

EOT;
?>
<tr>
	<td colspan="2">Please give a full name for the tournament, usually just location and year.<br />
	This is normally displayed as a heading in various places.</td>
</tr>
<tr>
	<td><b>Tournament Name</b></td>
<?php
print <<<EOT
	<td><input type="text" name="tname" size="40" value="{$tourn->display_name()}"></td>

EOT;
?>
</tr>
<tr>
	<td><b>Date (or first day)</b></td>
	<td><?php $tourn->Sdate->dateopt(); ?></td>
</tr>
<?php include 'php/sumchallenge.php'; ?>
<tr>
	<td><b>Click to continue</b></td>
	<td><input type="submit" value="Clone tournament"></td>
</tr>
</table>
</form>
</div>
</div>
</body>
</html>
