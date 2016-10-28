<?php
//   Copyright 2011 John Collins

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

include 'php/tcerror.php';
include 'php/session.php';
include 'php/checklogged.php';
include 'php/opendb.php';
include 'php/country.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = mysql_real_escape_string($value);
	$ret = mysql_query("select $column from player where $column='$qvalue'");
	if ($ret && mysql_num_rows($ret) != 0)  {
		include 'php/nameclash.php';
		exit(0);
	}
}

function checkname($newplayer) {
	$ret = mysql_query("select first,last from player where {$newplayer->queryof()}");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$column = "name";
		$value = $newplayer->display_name(false);
		include 'php/nameclash.php';
		exit(0);
	}
}

$playname = $_POST["playname"];
$email = $_POST["email"];
$club = $_POST["club"];
$country = $_POST["country"];
$rank = $_POST["rank"];
$passw1 = $_POST["passw1"];
$passw2 = $_POST["passw2"];
$nonbga = $_POST['nonbga'] != 'm';

try {
	opendb();
	$origplayer = new Player();
	$origplayer->frompost();
	$origplayer->fetchplayer();
}
catch (Tcerror $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}
	
// Check name changes and perform update if applicable
// Note that the "updatename" function does any consequent
// updates like changing team captain name if the player is a
// team captain.

$chname = false;
$newplayer = new Player($playname);
if  (!$origplayer->is_same($newplayer))  {
	checkname($newplayer);
	$origplayer->updatename($newplayer);
	$chname = true;
}
	
// Split down club into just club rather than club:country

$bits = split(':', $club);
if (count($bits) > 1)
	$club = $bits[0];

$origplayer->Rank = new Rank($rank);
$origplayer->Club = $club;
$origplayer->Country = $country;
$origplayer->Email = $email;
$origplayer->Nonbga = $nonbga;
$origplayer->update();
Club::optcreate_club($club, $country);
Country::optcreate_country($country);

$chpw = false;
try {
	if (strlen($passw1) != 0  &&  $passw1 != $origplayer->get_passwd())  {
		$chpw = true;
		$origplayer->set_passwd($passw1);
	}
}
catch (Tcerror $e) {
	$chpw = false;
}

$Title = "Player details updated OK";
include 'php/head.php';
print <<<EOT
<body>
<script language="javascript" src="webfn.js"></script>

EOT;
include 'php/nav.php';
print <<<EOT
<h1>$Title</h1>
<p>$Title.</p>

EOT;
if ($chname)
	print <<<EOT
<p>As you changed your name, you should probably logout and log back in again using the
menu on the left. This will reset any "cookies" with your original name in.</p>

EOT;
if ($chpw)
	print <<<EOT
<p>Password changed OK.</p>

EOT;
?>
</div>
</div>
</body>
</html>
