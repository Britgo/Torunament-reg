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
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename={$tourn->Tcode}.csv");
$q='"';
print "{$q}Name$q,{$q}Club$q,{$q}Rank$q,{$q}Email$q,{$q}Not BGA$q";
if ($tourn->Concess1 != 0)
	print ",$q{$tourn->Concess1name}$q";
if ($tourn->Concess2 != 0)
	print ",$q{$tourn->Concess2name}$q";
if ($tourn->Lunch != 0)
	print ",{$q}Lunch$q";
if (strlen($tourn->Dinner) != 0)
	print ",{$q}{$tourn->Dinner}$q";
print ",{$q}Fee$q,{$q}Date$q\n";
foreach ($players as $p)  {
	$nbga = $p->Nonbga? 1: 0;
	$c1 = $p->Concess1? 1: 0;
	$c2 = $p->Concess2? 1: 0;
	$lnch = $p->Lunch? 1: 0;
	$dinn = $p->Dinner? 1: 0;
	print "$q{$p->display_name()}$q,$q{$p->Club}$q,$q{$p->Rank->display()}$q,$q{$p->Email}$q,{$p->Nonbga}";
	if ($tourn->Concess1 != 0)
		print ",$c1";
	if ($tourn->Concess2 != 0)
		print ",$c2";
	if ($tourn->Lunch != 0)
		print ",$lnch";
	if (strlen($tourn->Dinner) != 0)
		print ",$dinn";
	print ",$p->Fee,$q{$p->Edate->shortdate()}$q\n";
}
?>
