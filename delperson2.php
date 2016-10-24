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
include 'php/tournclass.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/opendb.php';

try {
   opendb();
   $delpers = new Player();
   $delpers->frompost();
   if (!$delpers->isdefined())
   	throw new Tcerror("No delete person defined", "Delete error");
   $changeto = $_POST['chgname'];
   if (strlen($changeto) != 0  &&  $changeto != 'none:none')  {
   	$cbits = split(':', $changeto);
   	if (count($cbits) != 2)
   		throw new Tcerror("Not 2 fields in $changeto", "Input error");
   	$replpers = new Person($cbits[0], $cbits[1]);
   	$setcomm = "first='{$replpers->qfirst()}',last='{$replpers->qlast()}'";
   	$sel = $delpers->queryof();
   	$plushist = isset($_POST['adjhist']);
   	$tournlist = get_tcodes("tcode", false, $plushist);
   	foreach ($tournlist as $tc)  {
   		$ents = $tc . "_entries";
   		$ret = mysql_query("UPDATE $ents SET $setcomm WHERE $sel");
   		if (!$ret)
   			throw new Tcerror(mysql_error(), "Update entry error");
   	}
   }
   $delpers->delete_player();
}
catch (Tcerror $e)  {
   $Title = "Delete error ";
   $mess = $e->getMessage();
   include 'php/wrongentry.php';
   exit(0);
}
$dispname = $delpers->display_name();
$Title = "Deleted OK";
include 'php/head.php';
?>
<body onload="window.location=window.location.protocol+'//'+window.location.hostname+'/useradmin.php'">
<h1>Deleted OK</h1>
<?php
print <<<EOT
<p>The user $dispname has been deleted successfully.</p>

EOT;
?>
<p>Please <a href="useradmin.php">Click here</a> to return to the admin page.</p>
</body>
</html>
