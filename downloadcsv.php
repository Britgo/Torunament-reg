<?php

// Copyright John Collins 2014
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

include 'tcerror.php';
include 'tdate.php';
include 'rank.php';
include 'person.php';
include 'entrant.php';
include 'tournclass.php';
include 'opendb.php';

if (!isset($_GET['tcode']))  {
print <<<EOT
<h1>Wrong entry</h1>
<p>I do not know how you got here, but it is wrong</p>

EOT;
	return;
}

$tcode = $_GET['tcode'];

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
	$players = get_entrants($tourn);
}
catch (Tcerror $e)  {
	$hdr = $e->Header;
	$msg = htmlspecialchars($e->getMessage());
	print <<<EOT
<h1>$hdr</h1>
<p>$msg</p>

EOT;
	return;
}
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename={$tourn->Tcode}.csv");
$q='"';
print "{$q}Name$q,{$q}Not BGA$q";
if ($tourn->Concess1 != 0)
	print ",$q{$tourn->Concess1name}$q";
if ($tourn->Concess2 != 0)
	print ",$q{$tourn->Concess2name}$q";
if ($tourn->Lunch != 0)
	print ",{$q}Lunch$q";
print ",{$q}Fee$q\n";
foreach ($players as $p)  {
	$nbga = $p->Nonbga? 1: 0;
	$c1 = $p->Concess1? 1: 0;
	$c2 = $p->Concess2? 1: 0;
	$lnch = $p->Lunch? 1: 0;
	print "$q{$p->display_name()}$q,{$p->Nonbga}";
	if ($tourn->Concess1 != 0)
		print ",$c1";
	if ($tourn->Concess2 != 0)
		print ",$c2";
	if ($tourn->Lunch != 0)
		print ",$lnch";
	print ",$p->Fee\n";
}
?>
