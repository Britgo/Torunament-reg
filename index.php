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

$Title = 'BGA Tournament Registration';
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function okdel(url, descr) {
	if (!confirm("OK to delete tournament " + descr))
		return;
	document.location = "dt.php" + url;
}
</script>
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
		$tlist = get_tcodes("sdate desc,tname", false, false);
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
	<th>Entries</th>
	<th>Actions</th>
</tr>
EOT;

		foreach ($tlist as $tc)  {
			$tourn = new Tournament($tc);
			$tourn->fetchdets();
			$url = $tourn->urlof();
			$nameprin = $tourn->display_name();
			if  ($tourn->Open  &&  !$tourn->is_over())
				$nameprin = "<a href=\"eform.php$url\">$nameprin</a>";
			print <<<EOT
<tr>
	<td>$nameprin</td>
	<td>{$tourn->display_dates()}</td>
	<td>{$tourn->Nrounds}</td>
	<td><a href="listentries.php{$url}">{$tourn->count_entries()}</a></td>
	<td><a href="downloadics.php{$url}">ICS</a>

EOT;
			if ($organ)
				print "<a href=\"clonetourn.php$url\">Clone</a>";
			if ($admin || ($organ && $userid == $tourn->Orguser))  {
				print <<<EOT
&nbsp;<a href="javascript:okdel('$url', '{$tourn->display_code()}');">Del</a>

EOT;
			if  (!$tourn->is_over())
				print <<<EOT
&nbsp;<a href="upddescr.php{$url}">Update description</a>&nbsp;
<a href="updfeedates.php{$url}">Update dates, fees</a></td>
EOT;
			print <<<EOT
</td>
</tr>

EOT;
			}
		}
		print <<<EOT
</table>
<p>Click on the tournament name to enter for the tournament or amend your existing entry.
Click on the number of entrants to view the current list of entrants. The ICS link downloads an ICS
file which can be incorporated in calendar applications.</p>

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
