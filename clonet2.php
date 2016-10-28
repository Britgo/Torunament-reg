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
include 'php/opendb.php';
include 'php/club.php';
include 'php/country.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/entrant.php';
include 'php/tdate.php';
include 'php/tournclass.php';

if (!isset($_POST['tcode'])  ||  !isset($_POST['ptcode']))  {
	$mess = "No codes";
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_POST['tcode'];
$ptcode = $_POST['ptcode'];

// Check anti-spam sum

include 'php/checksum.php';

// Check the guy can create tournaments before we go any further

if (!$organ)  {
	$mess = 'Not Tournament Organiser';
	include 'php/wrongentry.php';
	exit(0);
}

try {
	opendb();
	$tourn = new Tournament($tcode);
	$ptourn = new Tournament($ptcode);
	$ptourn->fetchdets();
	$tourn->clonefrom($ptourn);
	$tourn->Name = trim($_POST['tname']);
	$tourn->Sdate->frompost();
	$tourn->Ebdate = new Tdate($tourn->Sdate);
	$tourn->Ebdate->incdays($ptourn->Sdate->daysbetween($ptourn->Ebdate));
	$tourn->Provisional = true;
 	$tourn->Open = false;
 	$tourn->Orguser = $userid;
	$tourn->create();
}
catch (Tcerror $e)  {
	$mess = 'Cannot open database or create tournament' . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$Title = "Tournament cloned OK";
include 'php/head.php';
?>
<body>
<?php
include 'php/nav.php';
print <<<EOT
<h1>Tournament cloned OK</h1>
<p>Your tournament, {$tourn->display_name()}, was cloned successfully, however you will probably need to amend some details.</p>

EOT;
?>
</div>
</div>
</body>
</html>
