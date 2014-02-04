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

if (!isset($_GET['tcode'])  || !isset($_GET['f'])  ||  !isset($_GET['l']))  {
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
	$pers = new Entrant($_GET['f'], $_GET['l']);
	$pers->del($tourn);
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

header("Location: http://www.britgo.org/tournaments/_register/listall{$tourn->urlof()}");
?>
