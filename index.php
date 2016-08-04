<?php
$Title = 'BGA Tournament Registration';
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
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
	if ($organ)
		$list = get_tcodes("sdate desc,tname", false, false);
	else
		$tlist = get_tcodes("sdate desc,tname", true, true);
	if  (count($tlist) == 0)
		print "<p>There are currently no tournaments to list.</p>\n";
	else  {
		print <<<EOT
<table cellpadding="2" cellspacing="2">
<tr>
	<th>Name</th>
	<th>Date(s)</th>
	<th>Rounds</th>
	<th>ICS</th>
</tr>
EOT;

		foreach ($tlist as $tc)  {
			$tourn = new Tournament($tc);
			$tourn->fetchdets();
			$url = $tourn->urlof();
			$nameprin = $tourn->display_name();
			if  ($tourn->Open)  {
				if  (!$tourn->is_over())
					$nameprin = "<a href=\"eform.php$url\">$nameprin</a>";
			}
			print <<<EOT
<tr>
	<td>$nameprin</td>
	<td>{$tourn->display_dates()}</td>
	<td>{$tourn->Nrounds}</td>
	<td><a href="downloadics.php{$url}">ICS</a></td>
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
}
?>
</div>
</div>
</body>
</html>
