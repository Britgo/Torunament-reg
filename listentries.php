<?php

//   Copyright 2016 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

// Set up everything from functions

include 'php/tcerror.php';
include 'php/session.php';
include 'php/tdate.php';
include 'php/club.php';
include 'php/country.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/entrant.php';
include 'php/tournclass.php';
include 'php/opendb.php';

if (!isset($_GET['tcode']))  {
	$mess = 'No tournament given';
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_GET['tcode'];

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
	$entlist = get_entrants($tourn);
}
catch (Tcerror $e)  {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$mytourn = $admin || ($organ && $tourn->Orguser == $userid);

$Title = "Entrants for {$tourn->display_name()}";
include 'php/head.php';
print <<<EOT
<body>

EOT;

// If organiser, put in JavaScript func

if ($mytourn)
	print <<<EOT
<script language="javascript">
function okdel(urlpar) {
	if  (confirm("OK to remove player from {$tourn->display_name()}"))
		document.location = "delentry.php" + urlpar;
}
</script>

EOT;

include 'php/nav.php';

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
	if  ($mytourn)
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
		if  (!$mytourn)
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
	if ($mytourn && preg_match('/@/', $player->Email)) {
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
	if ($mytourn)  {
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
<a href="javascript:okdel('{$tourn->urlof()}&{$player->urlof()}');">Del</a>
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
if ($mytourn)  {
	if  ($Nc1 > 0) print "$Nc1 {$tourn->display_concess1name()}\n";
	if  ($Nc2 > 0) print "$Nc2 {$tourn->display_concess2name()}\n";
}
if ($Nlunch > 0) print "$Nlunch for lunch\n";
if ($Ndinner > 0) print "$Ndinner for {$tourn->display_dinner()}\n";
if ($Npriv > 0) print "$Npriv private entries\n";
print "</p>\n";
if ($mytourn)
	print <<<EOT

<p>Click <a href="downloadgodraw.php{$tourn->urlof()}">here</a>
to download a GoDraw file with these entries in.</p>

<p>Click <a href="downloadcsv.php{$tourn->urlof()}">here</a>
to download a CSV file with these entries in containing name, club, email, rank, lunch, concession and fee options suitable for importing into a spreadsheet.</p>

EOT;
?>
</div>
</div>
</body>
</html>
