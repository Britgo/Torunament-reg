<?php

// Copyright John Collins 2014
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

include 'tcerror.php';
include 'tdate.php';
include 'person.php';
include 'tournclass.php';
include 'opendb.php';

function icsdisp($str)
{
	return preg_replace("/\r?\n/", '\\n' . "\n", $str) . '\\n';
}

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
header("Content-Type: text/calendar");
header("Content-Disposition: attachment; filename={$tourn->Tcode}.ics");
print <<<EOT
BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
UID:jmc@britgo.org
ORGANIZER;CN={$tourn->Contact->First} {$tourn->Contact->Last}:MAILTO:{$tourn->Email}
DTSTART:{$tourn->Sdate->icsdate()}

EOT;
if ($tourn->Ndays > 1) {
	$nd = new Tdate($tourn->Sdate);
	$nd->incdays($tourn->Ndays-1);
	print "DTEND:{$nd->icsdate()}\n";
}
print "SUMMARY:{$tourn->Name} Go Tournament\n";
print "DESCRIPTION:";
if (strlen($tourn->Format) != 0)
  print icsdisp($tourn->Format);
if (strlen($tourn->Overview) != 0)
  print icsdisp($tourn->Overview);
if (strlen($tourn->Address) != 0)
  print icsdisp($tourn->Address);
print "{$tourn->Postcode}\\n";
if (strlen($tourn->Website) != 0)  {
	$w = $tourn->Website;
	if (!preg_match('/^<a/', $w))  {
		if  (!preg_match('/^http:/', $w))
			$w = "http://$w";
		$w = "<a href=\"$w\">Website</a>";
	}
	print $w;
}
print <<<EOT

END:VEVENT
END:VCALENDAR

EOT;
?>
