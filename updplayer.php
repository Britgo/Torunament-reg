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
include 'php/tdate.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/club.php';
include 'php/country.php';
include 'php/opendb.php';

// Check the current user can do this before we go any further

if (!$admin)  {
	$mess = 'Not Admin Person';
	include 'php/wrongentry.php';
	exit(0);
}

try {
   opendb();
   $pers = new Player();
   $pers->fromget();
   $pers->fetchplayer();
   $clublist = list_clubs();
   $countrylist = list_countries();
}
catch (Tcerror $e)  {
   $Title = "Update error ";
   $mess = $e->getMessage();
   include 'php/wrongentry.php';
   exit(0);
}
$Title = "Update details of player";
include 'php/head.php';
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.plform;
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
    	if (!nonblank(form.club.value))  {
			alert("No club given");
      	return  false;
   	}
   	if (!nonblank(form.country.value))  {
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
	var fm = document.plform;
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
	var fm = document.plform;
	var cs = fm.countrysel;
	var csi = cs.selectedIndex;
	if  (csi <= 0)
		return;
	fm.country.value = cs.options[csi].value;
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Update user details</h1>
<p>Please use the form to update details of the player on the tournament registration system.</p>
<form name="plform" action="updplayer2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<?php
$pw = $pers->disp_passwd();
print <<<EOT
{$pers->save_hidden("orig")}
<table cellpadding="5" cellspacing="5" border="0">
<tr>
	<td>Player Name</td>
	<td><input type="text" name="playname" value="{$pers->display_name()}"></td>
</tr>
<tr>
	<td>If name changed, adjust name in current tournaments.</td>
	<td><input type="checkbox" name="adjcurr" checked></td>
</tr>
<tr>
	<td>If name changed, adjust name in historic tournaments.</td>
	<td><input type="checkbox" name="adjhist"></td>
</tr>
<tr>
	<td>Userid (set blank to cancel)</td>
	<td><input type="text" name="userid" value="{$pers->display_login()}"></td>
</tr>
<tr>
	<td>Password (set blank to let system set it)</td>
	<td><input type="password" name="passw1" value="$pw"></td>
</tr>
<tr>
	<td>Confirm Password (likewise)</td>
	<td><input type="password" name="passw2" value="$pw"></td>
</tr>
<tr>
	<td>Email (must have)</td>
	<td><input type="text" name="email" value="{$pers->display_email_nolink()}"></td>
</tr>
<tr>
	<td>Club</td>
	<td>
EOT;
$pers->clubopt("club_sel");
print <<<EOT
</td>
</tr>
<tr>
	<td>(Enter if not on drop-down)</td>
	<td><input type="text" name="club" value="{$pers->display_club()}" size="30"></td>
</tr>
<tr>
	<td>Country</td>
	<td>
EOT;
$pers->countryopt("country_sel");
print <<<EOT
</td>
</tr>
<tr>
	<td>(Enter if not on drop-down)</td>
	<td><input type="text" name="country" value="{$pers->display_country()}" size="30"></td>
</tr>
<tr>
	<td>Rank</td>
	<td>
EOT;
$pers->rankopt();
print <<<EOT
</td>
</tr>
<tr>
	<td>BGA Memb</td>
	<td>
EOT;
$pers->bgaopt(FALSE);
?>
</td>
</tr>
<tr>
	<td>User permissions</td>
	<td>
<?php
print <<<EOT
<input type="hidden" name="origpriv" value="{$pers->Admin}">
EOT;
if ($userpriv == 'SA')  {
	$Nsel = $Osel = $Asel = $SAsel = "";
	switch  ($pers->Admin)  {
		default: $Nsel = ' selected'; break;
		case  'O':	$Osel = ' selected'; break;
		case  'A':  $Asel = ' selected'; break;
		case  'SA': $SAsel = ' selected'; break;
	}
	print <<<EOT
<select name="privilege">
<option value="N"$Nsel>Normal User</option>
<option value="O"$Osel>Organiser</option>
<option value="A"$Asel>Administrator</option>
<option value="SA"$SAsel>Superuser</option>
</select>
EOT;
}
else {
	switch  ($pers->Admin)  {
	default:
		print <<<EOT
<input type="checkbox" name="setorg">Set as organiser
EOT;
		break;
	case 'O':
		print <<<EOT
<input type="checkbox" name="setorg" checked>Set as organiser
EOT;
		break;
	case 'A':case 'SA':
		print <<<EOT
User is set as administrator.
EOT;
		break;	
	}
}
?>
</td></tr>
</table>
<p>
<input type="submit" name="subm" value="Update User">
</p>
</form>
</div>
</div>
</body>
</html>
