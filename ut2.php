<?php

// Set up everything from functions

include 'tcerror.php';
include 'tdate.php';
include 'person.php';
include 'tournclass.php';
include 'opendb.php';

if (!isset($_POST['tcode']))  {
print <<<EOT
<h1>Wrong entry</h1>
<p>I do not know how you got here, but it is wrong</p>

EOT;
	return;
}

$tcode = $_POST['tcode'];

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->frompost();
	$tourn->update();
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

header("Location: http://www.stalbans-go.org.uk/tupdatedok?tcode=$tcode");
?>
