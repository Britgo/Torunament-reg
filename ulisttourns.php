<?php

// Set up everything from functions

include 'tcerror.php';
include 'tdate.php';
include 'person.php';
include 'tournclass.php';
include 'opendb.php';

try  {
	opendb();
	$tlist = get_tcodes("sdate,tname");
	if  (count($tlist) == 0)
		print "<p>There are currently no tournaments to list.</p>\n";
	else  {
		print <<<EOT
<table>
<tr>
	<th>Code</th>
	<th>Name</th>
	<th>Date(s)</th>
	<th>Venue</th>
	<th>Postcode</th>
	<th>Format</th>
	<th>Overview</th>
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
					$codeprin = "<a href=\"_register/entry$url\">$codeprin</a>";
				$nameprin = "<a href=\"_register/list$url\">$nameprin</a>";
			}
			print <<<EOT
<tr>
	<td>$codeprin</td>
	<td>$nameprin</td>
	<td>{$tourn->display_dates()}</td>
	<td>{$tourn->display_addr()}</td>
	<td>{$tourn->display_pc()}</td>
	<td>{$tourn->display_format()}</td>
	<td>{$tourn->display_over()}</td>
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
