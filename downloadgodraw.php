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
include 'php/club.php';
include 'php/country.php';
include 'php/tdate.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/entrant.php';
include 'php/tournclass.php';
include 'php/opendb.php';

if (!isset($_GET['tcode']))  {
	$mess = "No code";
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_GET['tcode'];

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
	$players = get_entrants($tourn);
}
catch (Tcerror $e)  {
	$mess = htmlspecialchars($e->getMessage());
	include 'php/wrongentry.php';
	exit(0);
}

header("Content-Type: text/plain");
header("Content-Disposition: attachment; filename={$tourn->Tcode}.txt");
print "NAME-FG\tGRADE\tCLUB\tCOUNTRY\r\n";
foreach ($players as $p)  {
	print "{$p->Last} {$p->First}\t";
	$rk = strtolower($p->Rank->display());
	print "$rk\t";
	print "{$p->Club}\t";
	print "{$p->Country}\r\n";
}
?>
