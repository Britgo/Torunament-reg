<?php
// Copyright John Collins 2016
// Licensed under the GPL, v3

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

?>
<div id="Nav">
<div class="innertube">
<table cellpadding="5" cellspacing="5" align="left">
<tr>
	<td><a href="https://www.britgo.org" title="Go to BGA main site"><img src="images/gohead12.gif" width="133" height="47" alt="BGA Logo" border="0" hspace="0" vspace="0"></a></td>
	<td><a href="index.php" title="Go to tournament registration home page" class="headlink">Tournament Registration Home</a></td>
	<td><a href="https://www.britgo.org/tournaments" title="View BGA tournament calendar" class="headlink">BGA Calendar</a><br/>
	<a href="https://www.britgo.org/results/12months" title="View last 12 months results" class="headlink">Results</a></td>
<?php
if ($organ) {
	print <<<'EOF'
	<td><a href='createtourn.php' title='Create a new tournament' class='oheadlink'>Create Tournaement</a></td>

EOF;
}
if ($admin) {
	print <<<'EOF'
	<td><a href='useradmin.php' title='Administer user file' class='aheadlink'>Administration</a></td>

EOF;
}
if ($logged_in) {
	$qu = htmlspecialchars($username);
	print <<<EOF
	<td><a href="ownupd.php" title="Update your account details" class="headlink">Update account</a></td>
	<td><a href="logout.php" title="Log yourself out" class="headlink">Logout<br>$qu</a></td>

EOF;
}
else {
	$userid = "";
	if (isset($_COOKIE['user_id']))
		$userid = $_COOKIE['user_id'];
	print <<<EOT
<td><form name="lifm" action="login.php" method="post" enctype="application/x-www-form-urlencoded">
User:<input type="text" name="user_id" id="user_id" value="$userid" size="10">
Password:<input type="password" name="passwd" size="10">
<input type="submit" value="Login">
</form><br />
<a href="javascript:lostpw();" title="Get your lost password">Lost password?</a>&nbsp;&nbsp;&nbsp;
<a href="newacct.php" title="Create yourself an account">Create account</a></td>

EOT;
}
?>
</tr>
</table>
</div>
</div>
<div id="Content">
<div class="innertube">
