<?php

// Set up everything from functions

include 'tcform.php';
include 'opendb.php';
include 'rank.php';
include 'player.php';

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
	tcform(false, $tourn, list_players());
}
catch (Tcerror $e)  {
	$hdr = $e->Header;
	$msg = $e->getMessage();
	print <<<EOT
<h1>$hdr</h1>
<p>$msg</p>

EOT;
	return;
}
?>
