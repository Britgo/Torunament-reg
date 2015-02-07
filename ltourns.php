<script lanaguage="javascript">
function dodelete(tc)
{
	if  (!confirm("Are you sure you want to delete tournament " + tc))
		return;
	document.location = "delete?tcode=" + tc;
}
</script>

<?php

// Set up everything from functions

include 'tcerror.php';
include 'tdate.php';
include 'person.php';
include 'tournclass.php';
include 'opendb.php';

try  {
	opendb();
	$tlist = get_tcodes("sdate desc,tname");
	if  (count($tlist) == 0)
		print "<p>There are currently no tournaments to list.</p>\n";
	else  {
		print <<<EOT
<table>
<tr>
	<th>Clone</th>
	<th>Date(s)</th>
	<th>Name</th>
	<th>Rounds</th>
	<th>Entry</th>
	<th>List</th>
	<th>Upd</th>
	<th>Del</th>
</tr>
EOT;

		foreach ($tlist as $tc)  {
			$tourn = new Tournament($tc);
			$tourn->fetchdets();
			$url = $tourn->urlof();
			print <<<EOT
<tr>
	<td><a href="clone$url">$tc</a></td>
	<td>{$tourn->display_dates()}</td>
	<td>{$tourn->display_name()}</td>
	<td>{$tourn->Nrounds}</td>

EOT;
	if ($tourn->is_over())
		print <<<EOT
	<td>entry</td>
	<td><a href="listall$url">list</a></td>
	<td>update</td>
	<td><a href="javascript:dodelete('$tc')">delete</a></td>
</tr>

EOT;
	else
		print <<<EOT
	<td><a href="form$url">entry</a></td>
	<td><a href="listall$url">list</a></td>
	<td><a href="update$url">update</a></td>
	<td><a href="javascript:dodelete('$tc')">delete</a></td>
</tr>

EOT;
		}
		print <<<EOT
</table>
EOT;
	}
	print <<<EOT
<p><a href="create">Click here</a> to set up a new tournament. Click on a tournament code above
to set up a new tournament based on that one.</p>

EOT;
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
