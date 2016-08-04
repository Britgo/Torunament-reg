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

$Title = "BGA Tournament Registration Account";
include 'php/tcerror.php';
include 'php/head.php';
include 'php/opendb.php';
include 'php/country.php';
include 'php/club.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
try {
	opendb();
}
catch (Tcerror $e)  {
	print <<<EOT
<body>
<p>Cannot open database.</p>
</body>
</html>

EOT;
	exit(0);
}
?>
<body>
<script language="javascript" src="webfn.js"></script>
<script language="javascript">
function formvalid()
{
      var form = document.trform;
      if (form.turnoff.checked) {
      	alert("You didn't turn off the non-spammer box");
      	return false;
      }
      if (!form.turnon.checked) {
      	alert("You didn't turn on the non-spammer box");
      	return false;
      }
      if  (!okname(form.playname.value))  {
         alert("Invalid player name given");
         return false;
      }
      if  (!/^\w+$/.test(form.userid.value))  {
      	alert("No valid userid given");
      	return  false;
      }
      if  (!nonblank(form.email.value))  {
      	alert("No email address given");
      	return  false;
      }
      if (form.passw1.value != form.passw2.value)  {
      	alert("Passwords do not match");
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
	var fm = document.trform;
	var ps = fm.clubsel;
	var cs = fm.countrysel;
	var psi = ps.selectedIndex;
	if  (psi < 0)
		return;
	var optv = ps.options[psi].value;
	var parts = optv.split(':');
	var cntry = parts[1];
	var n;
	alert(cntry);
	for (n = 1;  n < cs.options.length; n++) {
		alert(cs.options[n].value);
		if (cs.options[n].value == cntry)  {
			cs.selectedIndex = n;
			break;
		}
	}
}
</script>
<?php include 'php/nav.php'; ?>
<h1>Apply for new account on tournament registration database</h1>
<p>Please use the form below to apply for an account on the tournament registration database.
You will only need an account if you want to amend or delete an entry you've made or you want the system to
remember details such as your email address.
</p>
<p><b>Please</b> don't try to create multiple accounts under different names!
If you have forgotten your password, select the "remind password" entry.
</p>
<p>Please note that email addresses are <b>not</b> published anywhere or used other than to send you confirmation emails of
tournament entries.</p>
<form name="trform" action="newacct2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return formvalid();">
<table cellpadding="5" cellspacing="5" border="0">
<tr><td>Player Name</td>
<td><input type="text" name="playname"></td></tr>
<tr><td>Userid (initials acceptable)</td>
<td><input type="text" name="userid"></td></tr>
<tr><td>Password (leave blank to let system set it)</td>
<td><input type="password" name="passw1"></td></tr>
<tr><td>Confirm Password (likewise)</td>
<td><input type="password" name="passw2"></td></tr>
<tr><td>Email (must have)</td>
<td><input type="text" name="email"></td></tr>

<tr><td>Club</td>
<td>
<?php
$player = new Player();
$player->clubopt("club_sel");
print <<<EOT
</td></tr>
<tr><td>Country</td><td>
EOT;
$player->countryopt();
print <<<EOT
<tr><td>Rank</td><td>
EOT;
$player->rankopt();
print <<<EOT
</td></tr>
<tr><td>BGA Memb</td><td>
EOT;
$player->bgaopt();
print "</td></tr>\n";
?>
<tr><td colspan=2><input type="checkbox" name="turnoff" checked>
&lt;&lt; Because I'm not a spammer I'm turning this off and this on &gt;&gt;
<input type="checkbox" name="turnon"></td></tr>
</table>
<p>
<input type="submit" name="subm" value="Create Account">
</p>
</form>
</div>
</div>
</body>
</html>
