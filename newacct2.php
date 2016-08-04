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

// Clog up the works for spammers

//if (isset($_POST["turnoff"]) || !isset($_POST["turnon"]))  {
//	system("sleep 60");
//	exit(0);
//}

include 'php/tcerror.php';
include 'php/opendb.php';
include 'php/country.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/genpasswd.php';
include 'php/newaccemail.php';

$playname = $_POST["playname"];
$userid = $_POST["userid"];
$passw1 = $_POST["passw1"];
$passw2 = $_POST["passw2"];
$email = $_POST["email"];
$club = $_POST["clubsel"];
$country = $_POST["countrysel"];
$rank = $_POST["rank"];

if  (strlen($playname) == 0)  {
	$mess = "No player name given";
	include 'php/wrongentry.php';
	exit(0);
}
if  (strlen($userid) == 0)  {
	$mess = "No user name given";
	include 'php/wrongentry.php';
	exit(0);
}

//  Get player name and check he doesn't clash

try {
	opendb();
	$player = new Player($playname);
}
catch (Tcerror $e) {
   $mess = $e->getMessage();
   include 'php/wrongentry.php';
   exit(0);
}

$ret = mysql_query("select first,last from player where {$player->queryof()}");
if ($ret && mysql_num_rows($ret) != 0)  {
	$column = "name";
	$value = $player->display_name(false);
	include 'php/nameclash.php';
	exit(0);
}

function checkclash($column, $value) {
	if (strlen($value) == 0)
		return;
	$qvalue = mysql_real_escape_string($value);
	$ret = mysql_query("select $column from player where $column='$qvalue'");
	if ($ret && mysql_num_rows($ret) != 0)  {
		$row = mysql_fetch_array($ret);
		$column = htmlspecialchars($column);
		$value =  htmlspecialchars($row[0]);
		include 'php/nameclash.php';
		exit(0);
	}
}

// Split down club into just club rather than club:country

$bits = split(':', $club);
if (array_count($bits) > 1)
	$club = $bits[0];

// Check user name doesn't clash

checkclash('user', $userid);

$player->Rank = new Rank($rank);
$player->Club = $club;
$player->Country = $country;
$player->Email = $email;
$player->Userid = $userid;

try {
	$player->create_or_update();

	// If no password specified, invent one

	if (strlen($passw1) == 0)
		$passw1 = generate_password();

	$player->set_passwd($passw1, $userid);
}
catch (Tcerror $e) {
	$h = htmlspecialchars($e->Header);
	$m = htmlspecialchars($e->getMessage());
	$Title = "DB upd error";
	include 'php/head.php';
	print <<<EOT
<body>
<h1>$h</h1>
<p>Database error was $m.</p>
</body>
</html>

EOT;
	exit(0);
}
newaccemail($email, $userid, $passw1);
$Title = "BGA Tournament Registration Account Created";
include 'php/head.php';
?>
<body>
<?php include 'php/nav.php';
print <<<EOT
<h1>$Title</h1>
<p>Your account $userid has been successfully created and you should be receiving
a confirmatory email with your password in.</p>

EOT;
?>
</div>
</div>
</body>
</html>
