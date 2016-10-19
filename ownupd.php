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
include 'php/checklogged.php';
include 'php/opendb.php';
include 'php/country.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';

try {
	opendb();
	$player = new Player();
	$player->fromid($userid);
}
catch (Tcerror $e) {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$disp_userid = htmlspecialchars($userid);
$Title = "Update Your Details";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.playform;
      if  (!okname(form.playname.value))  {
         alert("Invalid player name given");
         return false;
      }
      if  (!nonblank(form.email.value))  {
      	alert("No email address given");
      	return  false;
      }
      if (form.passw1.value != form.passw2.value)  {
      	alert("Passwords do not match");
      	return  false;
      }
	   if (!nonblank(fm.club.value))  {
			alert("No club given");
      	return  false;
   	}
   	if (!nonblank(fm.country.value))  {
     		alert("No country given");
      	return  false;
   	}
      var nbgasel = form.nonbga;
	 	var optl = nbgasel.options;
	 	if (optl[nbgasel.selectedIndex].value == 'u')  {
	     alert("Please select BGA membership");
	     return  false;
	 	}
		return true;
}

function club_sel() {
	var fm = document.playform;
	var ps = fm.clubsel;
	var cs = fm.countrysel;
	var psi = ps.selectedIndex;
	if  (psi <= 0)
		return;
	var optv = ps.options[psi].value;
	var parts = optv.split(':');
	fm.club.value = parts[0];
	var cntry = parts[1];
	var n;
	for (n = 1;  n < cs.options.length; n++) {
		if (cs.options[n].value == cntry)  {
			cs.selectedIndex = n;
			break;
		}
	}
	fm.country.value = cntry;
}

function country_sel() {
	var fm = document.playform;
	var cs = fm.countrysel;
	var csi = cs.selectedIndex;
	if  (csi <= 0)
		return;
	fm.country.value = cs.options[csi].value;
}

function clubedited()  {
	var fm = document.playform;
	fm.clubsel.selectedIndex = -1;
}
function countryedited()  {
	var fm = document.playform;
	fm.countrysel.selectedIndex = -1;
}

function confirmdel(urlq)
{
	if  (!confirm("Please confirm you want to delete yourself"))
		return;
	document.location = "deluserid.php?" + urlq;
}
</script>
<?php
include 'php/nav.php';
print <<<EOT
<h1>Update Details for userid $disp_userid</h1>

EOT;
?>
<p>Please update your details as required using the form below.</p>
<p>Please note that email addresses are <b>not</b> published anywhere.</p>
<p>If you want to delete the records of your user name and user id, then please
<?php
print <<<EOT
<a href="javascript:confirmdel('{$player->urlof()}');">click here</a>.</p>
<form name="playform" action="ownupd2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
{$player->save_hidden()}
<table cellpadding="2" cellspacing="5" border="0">
<tr><td>Player Name</td>
<td><input type="text" name="playname" value="{$player->display_name()}"></td></tr>
<tr><td>Club</td>
<td>
EOT;
$player->clubopt("club_sel");
$qclub = htmlspecialchars($player->Club);
print <<<EOT
</td></tr><tr><td>(Enter if not on drop-down)</td><td><input type="text" name="club" value="$qclub" size="30" onchange="clubedited"></td></tr>
<tr><td>Country</td><td>

EOT;
$player->countryopt("country_sel");
$qcountry = htmlspecialchars($defplayer->Country);
print <<<EOT
</td></tr><tr><td>(Enter if not on drop-down)</td><td><input type="text" name="country" value="$qcountry" size="30" onchange="countryedited"></td></tr>
<tr><td>Rank</td><td>

EOT;
$player->rankopt();
print <<<EOT
</td></tr>
<tr><td>BGA Memb</td><td>

EOT;
$player->bgaopt(false);
print <<<EOT
</td></tr>

EOT;

$dp = $player->disp_passwd();
if (strlen($dp) != 0)
	$dp = " value=\"" . $dp . "\"";
print <<<EOT
<tr><td>Email</td><td><input type="text" name="email" value="{$player->display_email_nolink()}"></td></tr>
<tr><td>Password</td><td><input type="password" name="passw1"$dp></td></tr>
<tr><td>Confirm</td><td><input type="password" name="passw2"$dp></td></tr>

EOT;
?>
</table>
<p><input type="submit" name="subm" value="Update Details"></p>
</form>
</div>
</div>
</body>
</html>
