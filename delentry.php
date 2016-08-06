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
include 'php/rank.php';
include 'php/person.php';
include 'php/entrant.php';
include 'php/tournclass.php';
include 'php/opendb.php';

if (!$admin)  {
	$mess = 'Not admin';
	include 'php/wrongentry.php';
	exit(0);
}

if (!isset($_GET['tcode'])  || !isset($_GET['f'])  ||  !isset($_GET['l']))  {
	$mess = 'Codes not set';
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_GET['tcode'];

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
	$pers = new Entrant();
	$pers->fromget();
	$pers->del($tourn);
}
catch (Tcerror $e)  {
	$mess = 'Invalid codes';
	include 'php/wrongentry.php';
	exit(0);
}

$Title = 'Entrant deleted';
include 'php/head.php';
?>
<body>
<?php
include 'php/nav.php';
print <<<EOT
<h1>Entrant deleted</h1>
<p>The entrant {pers->display_name()} has been removed from {$tourn->display_name()}.</p>

EOT;
?>
</div>
</div>
</body>
</html>