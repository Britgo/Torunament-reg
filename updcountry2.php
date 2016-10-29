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
include 'php/tdate.php';
include 'php/club.php';
include 'php/country.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/tournclass.php';
include 'php/opendb.php';

$sendmsg = false;

try {
   opendb();
   $updcountry = new Country();
   $updcountry->from_post("orig");
   $newcountry = new Country();
   $newcountry->from_post();
   if  (!$updcountry->is_same($newcountry))  {
   	if ($newcountry->check_clashes())
   		throw new Tcerror("Country name clashes with existing", "Country name clash");
   	$updcountry->update($newcountry);
   	$qocnt = mysql_real_escape_string($updcountry->Name);
   	$qncnt = mysql_real_escape_string($newcountry->Name);
   	$sel = "country='$qocnt'";
   	$setcomm = "country='$qncnt'";
   	if  (isset($_POST['adjplayers'])) {
   		$ret = mysql_query("UPDATE player SET $setcomm WHERE $sel");
   		if (!$ret)
   			throw new Tcerror(mysql_error(), "Update player error");
   	}
   	if  (isset($_POST['adjcurr']))
   		Tournament::update_all_entries($sel, $setcomm, isset($_POST['adjhist']));
   }
}
catch (Tcerror $e)  {
   $Title = $e->Header;
   $mess = $e->getMessage();
   include 'php/generror.php';
   exit(0);
}
$dispname = $updcountry->display_name();
$Title = "Updated OK";
include 'php/head.php';
?>
<body onload="window.location=window.location.protocol+'//'+window.location.hostname+'/updcounts.php'">
<h1>Updated OK</h1>
<?php
print <<<EOT
<p>The country $dispname has been updated successfully.</p>

EOT;
?>
<p>Please <a href="updcounts.php">Click here</a> to return to the countries page.</p>
</body>
</html>
