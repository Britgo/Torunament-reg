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
include 'php/country.php';
include 'php/opendb.php';

try {
   opendb();
   $delcountry = new Country();
   $delcountry->fromget();
   $countlist = Country::list_countries();
}
catch (Tcerror $e)  {
   $Title = "Delete error ";
   $mess = $e->getMessage();
   include 'php/generror.php';
   exit(0);
}
$dispname = $delcountry->display_name();
$Title = "Delete Country";
include 'php/head.php';
?>
<body>
<?php
include 'php/nav.php';
print <<<EOT
<h1>Delete country $dispname</h1>
<form action="delcountry2.php" method="post" enctype="application/x-www-form-urlencoded">
{$delcountry->save_hidden("orig")}
<p>Use this form to delete country $dispname.</p>
<p>Please don't use this form to remove an incorrectly spelled country name and you haven't entered the
correct name, use <a href="updcountry.php?{$delcountry->urlof()}">this form</a> instead and amend the name.
Use this form either to remove a name completely, or where you've got two (or more) slightly different versions of the same name.</p>
<p>If you'd like to change entries in current tournaments to show a different country, please select that name from this list:
<select name="chgname">
<option value="none" selected>None</option>

EOT;
foreach ($countlist as $pc) {
	if ($pc->is_same($delcountry))
		continue;
	$ps = urlencode($pc->Name);
	print <<<EOT
<option value="$ps">{$pc->display_name()}</option>

EOT;
}
?>
</select></p>
<p>To change countries in player entries select this: <input type="checkbox" name="adjplayers" checked></p>
<p>If you also want to update historical tournament records with the other name, select this:
<input type="checkbox" name="adjhist"></p>
<p>Press <input type="submit" name="cont" value="Delete country"> to continue or <input type="button" name="canc" value="Cancel"  onclick="window.location=document.referrer;"></p>
</form>
</div>
</div>
</body>
</html>
