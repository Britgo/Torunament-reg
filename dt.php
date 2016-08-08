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
include 'php/person.php';
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
}
catch (Tcerror $e) {
	$mess = "Cannot open database " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
try  {
	$tourn = new Tournament($tcode);
	$tourn->del();
}
catch (Tcerror $e) {
	$mess = "Cannot delete tournament " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
$Title = "Tournament deleted OK";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>Tournament deleted OK</h1>
<?php
print <<<EOT
The tournament {$tourn->display_name()} was deleted OK.

EOT;
?>
</div>
</div>
</body>
</html>
