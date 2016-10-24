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

include 'php/tcerror.php';
include 'php/session.php';
include 'php/checkadmin.php';
include 'php/tdate.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/entrant.php';
include 'php/opendb.php';

try  {
	opendb();
}
catch (Tcerror $e) {
	$mess = "Cannot open database " . $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$Title = 'Update Player List';
include 'php/head.php';

?>
<body>
<?php include 'php/nav.php'; ?>
<h1>View/Update user details</h1>
<p>Click on user name to edit details.</p>
<table>
<tr>
	<th>Name</th>
	<th>Club</th>
	<th>Country</th>
	<th>Rank</th>
	<th>Login</th>
	<th>Type</th>
	<th>Action</th>
</tr>
<?php
$players = Player::list_players();
foreach ($players as $plyr)  {
	$urlp = $plyr->urlof();
	$disp = $plyr->display_name();
	print <<<EOT
<tr>
	<td><a href="updplayer.php?$urlp" class="nound">$disp</a></td>
	<td>{$plyr->display_club()}</td>
	<td>{$plyr->display_country()}</td>
	<td>{$plyr->display_rank()}</td>
	<td>{$plyr->display_login()}</td>
	<td>{$plyr->display_admin()}</td>
	<td><a href="delperson.php?$urlp">del</a></td>
</tr>

EOT;
}
?>
</table>
<p>Click <a href="updclubs.php">here</a> to update the clubs list or <a href="updcounts.php">here</a> to update the countries.</p>
</div>
</div>
</body>
</html>
