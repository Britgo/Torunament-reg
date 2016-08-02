<!DOCTYPE html>
<html>
<head>
<title>BGA Tournament Registration</title>
<meta name="generator" content="Bluefish 2.2.5" >
<meta name="author" content="John M Collins" >
<meta name="date" content="2016-08-02T16:18:59+0100" >
<meta name="copyright" content="">
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8">
<meta http-equiv="content-style-type" content="text/css">
<meta http-equiv="expires" content="0">
<link href="tournreg.css" type="text/css" rel="stylesheet"></link>
</head>
<body>
<?php
include 'php/session.php';
include 'php/nav.php';
include 'php/tcerror.php';
include 'php/tdate.php';
include 'php/person.php';
include 'php/tournclass.php';
include 'php/opendb.php';
?>
<h1>Tournament List</h1>
<?php

try  {
	opendb();
	$tlist = get_tcodes("sdate desc,tname", true, true);
	if  (count($tlist) == 0)
		print "<p>There are currently no tournaments to list.</p>\n";
	else  {
		print <<<EOT
<table cellpadding="2" cellspacing="2">
<tr>
	<th>Name</th>
	<th>Date(s)</th>
	<th>ICS</th>
</tr>
EOT;

		foreach ($tlist as $tc)  {
			$tourn = new Tournament($tc);
			$tourn->fetchdets();
			$url = $tourn->urlof();
			$codeprin = $tourn->display_code();
			$nameprin = $tourn->display_name();
			if  ($tourn->Open)  {
				if  (!$tourn->is_over())
					$codeprin = "<a href=\"http://www.britgo.org/tournaments/_register/form$url\">$codeprin</a>";
			}
			print <<<EOT
<tr>
	<td>$nameprin</td>
	<td>{$tourn->display_dates()}</td>
	<td><a href="downloadics.php{$tourn->urlof()}">ICS</a></td>
</tr>

EOT;
		}
		print <<<EOT
</table>

EOT;
	}
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
?>
</div>
</div>
</body>
</html>
