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
include 'php/club.php';
include 'php/country.php';
include 'php/opendb.php';

try  {
	opendb();
	$clublist = list_clubs();
}
catch (Tcerror $e) {
	$Title = $e->Header;
	$mess = $e->getMessage();
	include 'php/generror.php';
	exit(0);
}

$Title = 'Update Clubs List';
include 'php/head.php';

$Nclubs = count($clublist);
$Ncols = min(4, ceil($Nclubs/10));
$Nrows = ceil($Nclubs/$Ncols);

?>
<body>
<?php include 'php/nav.php'; ?>
<h1>View/Update club details</h1>
<p>Click on club name to edit details.</p>
<p>If there are two versions of the same club with similar names, delete the
one which is least accurate. You will get the option to reset club names in player lists.</p>
<table>
<tr>
<?php
for ($col = 0;  $col < $Ncols;  $col++)
	print <<<EOT
	<th>Name</th>
	<th>Country</th>
	<th>Action</th>
	
EOT;
?>
</tr>
<?php
for ($row = 0;  $row < $Nrows;  $row++)  {
	print "<tr>\n";
	for ($col = 0; $col < $Nclubs;  $col++)  {
		$n = $row + $col * $Nrows;
		if ($n >= $Nclubs)
			print "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";
		else  {
			$c = $clublist[$n];
			$urlc = $c->urlof();
			$disc = $c->display_name();
			print <<<EOT
	<td><a href="updclub.php?$urlc" class="nound">$disc</a></td>
	<td>{$c->display_country()}</td>
	<td><a href="delclub.php?$urlc">del</a></td>

EOT;
		}
	}
	print "</tr>\n";
}
?>
</table>
</div>
</div>
</body>
</html>
