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
include 'php/tdate.php';
include 'php/tournclass.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/player.php';
include 'php/opendb.php';
include 'php/genpasswd.php';
include 'php/newaccemail.php';

$sendmsg = false;

try {
   opendb();
   $updpers = new Player();
   $updpers->frompost("orig");
   if (!$updpers->isdefined())
   	throw new Tcerror("No update person defined", "Update error");
   $updpers->fetchplayer();

	// Get all the stuff

	$playname = $_POST["playname"];
	$login = $_POST["userid"];
	$passw1 = $_POST["passw1"];
	$passw2 = $_POST["passw2"];
	$email = $_POST["email"];
	$club = $_POST["club"];
	$country = $_POST["country"];
	$rank = $_POST["rank"];
	$nonbga = $_POST['nonbga'] != 'm';
	$adjcurr = isset($_POST['adjcurr']);
	$adjhist = isset($_POST['adjhist']);
	if ($userpriv == 'SA')
		$newupriv = $_POST['privilege'];
	elseif  ($updpers->Admin == 'A' || $updpers->Admin == 'SA')
		$newupriv = $updpers->Admin;
	elseif  (isset($_POST['setorg']))
		$newupriv = 'O';
	else
		$newupriv = 'N';

	$newpers = new Player($playname);
	if  ($newpers->isdefined()  &&  !$newpers->is_same($updpers))  {
		if  ($newpers->fetchplayer())
			throw new Tcerror("Trying to rename player to existing player", "Player exists");
		$updpers->updatename($newpers);
		if ($adjcurr)
			Tournament::update_all_entries($updpers->queryof(), "first='{$newpers->qfirst()}',last='{$newpers->qlast()}'", $plushist);
	}

	// Sort out what we're doing with passwords and userids

	if ($login == $updpers->Login)  {
		// Login hasn't changed - forget it if no login to begin with
		if (strlen($login) > 0)  {
			// Password has changed, reset it
			$oldpw = $updpers->get_passwd();
			if  ($oldpw != $passw1)  {
				$sendmsg = true;
				if  (strlen($passw1) == 0)
					$passw1 = generate_password();
				$updpers->set_passwd($passw1);
			}
		}
	}
	elseif (strlen($login) == 0)  {
		// Had login before, but cancelling it
		$updpers->set_passwd("","");
		if ($updpers->Admin != 'N')
			$updpers->set_admin('N');
	}
	elseif  (Player::check_clash_userid($login))
		throw new Tcerror("Userid $login clashes with existing user", "Clashing userid");	
	else  {
		$sendmsg = true;
		if  (strlen($passw1) == 0)
			$passw1 = generate_password();
		$updpers->set_passwd($passw1, $login);
	}

	// OK now update the rest

	if  ($updper->Admin != $newupriv)
		$updpers->set_admin($newupriv);

	$updpers->Club = $club;
	$updpers->Country = $country;
	$updpers->Nonbga = $nonbga;
	$updpers->Email = $email;
	$updpers->Rank = new rank($rank);
	$updpers->update();
}
catch (Tcerror $e)  {
   $Title = $e->Header;
   $mess = $e->getMessage();
   include 'php/generror.php';
   exit(0);
}
if ($sendmsg)
	newaccemail($email, $userid, $passw1);

$dispname = $updpers->display_name();
$Title = "Updated OK";
include 'php/head.php';
?>
<body onload="window.location=window.location.protocol+'//'+window.location.hostname+'/useradmin.php'">
<h1>Updated OK</h1>
<?php
print <<<EOT
<p>The user $dispname has been updated successfully.</p>

EOT;
?>
<p>Please <a href="useradmin.php">Click here</a> to return to the admin page.</p>
</body>
</html>
