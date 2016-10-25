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
include 'php/country.php';
include 'php/opendb.php';

try  {
	opendb();
	$countrylist = list_countries();
}
catch (Tcerror $e) {
	$Title = $e->Header;
	$mess = $e->getMessage();
	include 'php/generror.php';
	exit(0);
}

$Title = 'Update countries List';
include 'php/head.php';

$Ncountries = count($countrylist);
$Ncols = min(6, ceil($Ncountries/10));
$Nrows = ceil($Ncountries/$Ncols);
?>
<body>
<?php include 'php/nav.php'; ?>
<h1>View/Update country list</h1>
<p>Click on country name to edit details.</p>
<p>If there are two versions of the same country with similar names, delete the
one which is least accurate. You will get the option to reset country names in player or club lists.</p>
<table>
<tr>
<?php
for ($col = 0;  $col < $Ncols;  $col++)
	print <<<EOT
	<th>Country</th>
	<th>Action</th>

EOT;
?>
</tr>
<?php
for ($row = 0;  $row < $Nrows;  $row++)  {
	print "<tr>\n";
	for ($col = 0; $col < $Ncols;  $col++)  {
		$n = $row + $col * $Nrows;
		if ($n >= $Ncountries)
			print "<td>&nbsp;</td><td>&nbsp;</td>\n";
		else  {
			$c = $countrylist[$n];
			$urlc = $c->urlof();
			$disc = $c->display_name();
			print <<<EOT
	<td><a href="updcountry.php?$urlc" class="nound">$disc</a></td>
	<td><a href="delcountry.php?$urlc">del</a></td>

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
