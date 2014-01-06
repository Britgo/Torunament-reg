<?php

// Set up everything from functions

include 'tcform.php';
include 'opendb.php';
include 'rank.php';
include 'player.php';

try  {
	opendb();
	$tourn = new Tournament();
	tcform(true, $tourn, list_players());
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
