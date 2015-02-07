<?php

// Set up everything from functions

include 'tcerror.php';
include 'tdate.php';
include 'rank.php';
include 'person.php';
include 'entrant.php';
include 'tournclass.php';
include 'opendb.php';

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
	$entlist = get_entrants($tourn);
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
print <<<EOT
<h1>Entries for {$tourn->display_name()}</h1>

<table summary="Entry List">
<tr>
	<th>Name</th>
	<th>Club</th>
	<th>Country</th>
	<th>Grade</th>

EOT;
	if ($tourn->Lunch > 0)
		print <<<EOT
	<th>Lunch</th>

EOT;
	if (strlen($tourn->Dinner) != 0)
		print <<<EOT
	<th>{$tourn->display_dinner()}</th>
	
EOT;
	if  ($Everyone)
		print <<<EOT
<th>Status</th>
<th>Del</th>

EOT;
	print <<<EOT
</tr>

EOT;
$Nplayers = count($entlist); 
$Nc1 = $Nc2 = $Nlunch = $Ndinner = $Npriv = 0;
foreach ($entlist as $player) {
	$pb = "";
	$pa = "";
	if  ($player->Concess1)
		$Nc1++;
	if  ($player->Concess2)
		$Nc2++;
	if  ($player->Lunch)
		$Nlunch++;
	if  ($player->Dinner)
		$Ndinner++;
	if  ($player->Privacy)  {
		$Npriv++;
		if  (!$Everyone)
			continue;
		$pb = "(";
		$pa = ")";
	}
	$n = htmlspecialchars($pb . $player->First . ' ' . $player->Last . $pa);
	$cl = htmlspecialchars($player->Club);
	$cn = htmlspecialchars($player->Country);
	$rk = $player->Rank->display();
	$lunch = $player->Lunch? "Yes" : "No";
	$dinner = $player->Dinner? "Yes" : "No";
	if ($Everyone && preg_match('@', $player->Email)) {
		$ml = htmlspecialchars($player->Email);
		print  <<<EOT
<tr>
	<td><a href="mailto:$ml?subject=Tournament%20registration">$n</a></td>

EOT;
	}
	else
		print <<<EOT
<tr>
	<td>$n</td>

EOT;
	print <<<EOT
	<td>$cl</td>
	<td>$cn</td>
	<td>$rk</td>

EOT;
	if ($tourn->Lunch > 0)
		print <<<EOT
	<td>$lunch</td>

EOT;
	if (strlen($tourn->Dinner) != 0)
		print <<<EOT
	<td>$dinner</td>

EOT;
	if ($Everyone)  {
		print "<td>";
		if ($player->Concess1)
			print $tourn->display_concess1name();
		elseif ($player->Concess2)
			print $tourn->display_concess2name();
		else 
			print "Std";
		print <<<EOT
</td>
<td>
<a href="/tournreg/delentry.php{$tourn->urlof()}&{$player->urlof()}">Del</a>
</td>

EOT;
	}
	print <<<EOT
</tr>

EOT;
}
print <<<EOT
</table>
<p>Total $Nplayers attending

EOT;
if ($Everyone)  {
	if  ($Nc1 > 0) print "$Nc1 {$tourn->display_concess1name()}\n";
	if  ($Nc2 > 0) print "$Nc2 {$tourn->display_concess2name()}\n";
}
if ($Nlunch > 0) print "$Nlunch for lunch\n";
if ($Ndinner > 0) print "$Ndinner for {$tourn->display_dinner()}\n";
if ($Npriv > 0) print "$Npriv private entries\n";
print "</p>\n";
if ($Everyone)
	print <<<EOT

<p>Click <a href="http://www.britgo.org/tournreg/downloadgodraw.php{$tourn->urlof()}">here</a>
to download a GoDraw file with these entries in.</p>

<p>Click <a href="http://www.britgo.org/tournreg/downloadcsv.php{$tourn->urlof()}">here</a>
to download a CSV file with these entries in suitable for importing into a spreadsheet.</p>


EOT;
?>
