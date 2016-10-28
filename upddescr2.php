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

if (!isset($_POST['tcode']))  {
	$mess = "No code";
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_POST['tcode'];

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
	$tourn->fetchdets();
	$tourn->Name = trim($_POST['tname']);
	$tourn->Tclass = $_POST['tclass'];
	$tourn->Format = trim($_POST['format']);
	$tourn->Overview = trim($_POST['overview']);
	$tourn->Address = trim($_POST['address']);
	$tourn->Postcode = trim($_POST['postcode']);
	$diffdays = $tourn->Sdate->daysbetween($tourn->Ebdate);
	$tourn->Sdate->frompost();
	$tourn->Ebdate = new Tdate($tourn->Sdate);
	$tourn->Ebdate->incdays($diffdays);
	$tourn->Ndays = $_POST['ndays'];
 	$tourn->Nrounds = $_POST['rounds'];
 	$tourn->Provisional = isset($_POST["provisional"]);
 	$tourn->Open = isset($_POST["open"]);
 	$tourn->Orguser = trim($_POST['organiser']);
 	$tourn->Contact = new Person($_POST['contact']);
 	$tourn->Email = trim($_POST['email']);
	$tourn->Website = trim($_POST['website']);
	$tourn->update();
}
catch (Tcerror $e)  {
	$mess = 'Cannot open database or create tournament' . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$Title = "Tournament updated OK";
include 'php/head.php';
?>
<body>
<?php
include 'php/nav.php';
print <<<EOT
<h1>Tournament updated OK</h1>
<p>Your tournament, {$tourn->display_name()}, was updated successfully. If you want to change fees etc,
<a href="updfeedates.php{$tourn->urlof()}">Click Here</a>.</p>

EOT;
?>
</div>
</div>
</body>
</html>
