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
header("Content-Type: text/godraw");
header("Content-Disposition: attachment; filename={$tourn->Tcode}.gdi");
print "NAME-FG\tGRADE\tCLUB\tCOUNTRY\r\n";
foreach ($players as $p)  {
	print "{$p->Last} {$p->First}\t";
	$rk = strtolower($p->Rank->display());
	print "$rk\t";
	print "{$p->Club}\t";
	print "{$p->Country}\r\n";
}
?>
