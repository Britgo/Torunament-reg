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
	$tourn->Sdate->frompost();
	$tourn->Ebird = $_POST['ebird'];
	$tourn->Ebdate->frompost('eb');
	$tourn->Ndays = $_POST['ndays'];
 	$tourn->Nrounds = $_POST['rounds'];
 	$tourn->Provisional = isset($_POST["provisional"]);
 	$tourn->Open = isset($_POST["open"]);
	$tourn->Fee = $_POST['fee'];
	$tourn->Lunch = $_POST['lunch'];
	$tourn->Dinner = "";
	if (isset($_POST['dinner']))
		$tourn->Dinner = trim($_POST['dinner']);
 	$tourn->Concess1 = $_POST['concess1'];
 	$tourn->Concess2 = $_POST['concess2'];
 	$tourn->Concess1name = trim($_POST['concess1name']);
 	$tourn->Concess2name = trim($_POST['concess2name']);
 	$tourn->Nonbga = $_POST['nonbga'];
 	$tourn->Latefee = $_POST['latefee'];
 	$tourn->Latedays = $_POST['latedays'];
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
<p>Your tournament, {$tourn->display_name()}, was updated successfully. If you want to change description etc,
<a href="upddescr.php{$tourn->urlof()}">Click Here</a>.</p>

EOT;
?>
</div>
</div>
</body>
</html>
