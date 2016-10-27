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
include 'php/checkadmin.php';
include 'php/club.php';
include 'php/country.php';
include 'php/opendb.php';

try {
   opendb();
   $updclub = new Club();
   $updclub->fromget();
   $countrylist = list_countries();
}
catch (Tcerror $e)  {
   $Title = $e->Header;
   $mess = $e->getMessage();
   include 'php/generror.php';
   exit(0);
}
$Title = "Update details of club";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.clform;
    	if (!nonblank(form.clubname.value))  {
			alert("No club name given");
      	return  false;
   	}
   	if (!nonblank(form.countname.value))  {
     		alert("No country name given");
      	return  false;
   	}
		return true;
}

function country_sel() {
	var fm = document.clform;
	var cs = fm.countrysel;
	var csi = cs.selectedIndex;
	if  (csi <= 0)
		return;
	fm.countname.value = cs.options[csi].value;
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Update club details</h1>
<p>Please use the form to update details of the club on the tournament registration system.</p>
<?php
print <<<EOT
<p>If you really meant to just delete this club, please use <a href="delclub.php?{$updclub->urlof()}">this form</a> instead.</p>

EOT;
?>
<form name="clform" action="updclub2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
print <<<EOT
{$updclub->save_hidden("orig")}
<table cellpadding="5" cellspacing="5" border="0">
<tr>
	<td>Club Name</td>
	<td><input type="text" name="clubname" value="{$updclub->display_name()}"></td>

EOT;
?>
</tr>
<tr>
	<td>Adjust in player lists.</td>
	<td><input type="checkbox" name="adjplayers" checked></td>
</tr>
<tr>
	<td>Adjust in current tournaments.</td>
	<td><input type="checkbox" name="adjcurr" checked></td>
</tr>
<tr>
	<td>Also adjust in historic tournaments.</td>
	<td><input type="checkbox" name="adjhist"></td>
</tr>
<tr>
	<td>Country</td>
	<td>
<?php
$updclub->countryopt("country_sel");
print <<<EOT
</td>
</tr>
<tr>
	<td>(Enter if not on drop-down)</td>
	<td><input type="text" name="countname" value="{$updclub->display_country()}" size="30"></td>

EOT;
?>
</tr>
</table>
<p><input type="submit" name="subm" value="Update Club"></p>
</form>
</div>
</div>
</body>
</html>
