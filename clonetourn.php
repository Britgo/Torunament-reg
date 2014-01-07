<?php

// Copyright John Collins 2014
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

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
	$ptourn = new Tournament($tcode);
	$ptourn->fetchdets();
	$tourn->clonefrom($ptourn);
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
