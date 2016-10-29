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
   $updcountry = new Country();
   $updcountry->fromget();
}
catch (Tcerror $e)  {
   $Title = $e->Header;
   $mess = $e->getMessage();
   include 'php/generror.php';
   exit(0);
}
$Title = "Update details of country";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.coform;
   	if (!nonblank(form.countryname.value))  {
     		alert("No country name given");
      	return  false;
   	}
		return true;
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Update country</h1>
<p>Please use the form to update a country name on the tournament registration system.</p>
<?php
print <<<EOT
<p>If you really meant to just delete this country, please use <a href="delcountry.php?{$updcountry->urlof()}">this form</a> instead.</p>

EOT;
?>
<form name="coform" action="updcountry2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<?php
print <<<EOT
{$updcountry->save_hidden("orig")}
<table cellpadding="5" cellspacing="5" border="0">
<tr>
	<td>Country Name</td>
	<td><input type="text" name="countryname" value="{$updcountry->display_name()}"></td>

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
</table>
<p><input type="submit" name="subm" value="Update Country"></p>
</form>
</div>
</div>
</body>
</html>
