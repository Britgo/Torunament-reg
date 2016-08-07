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
include 'php/tdate.php';
include 'php/rank.php';
include 'php/person.php';
include 'php/entrant.php';
include 'php/tournclass.php';
include 'php/opendb.php';
include 'php/player.php';
include 'php/club.php';
include 'php/country.php';

if (!isset($_POST['tcode']))  {
	$mess = "No code";
	include 'php/wrongentry.php';
	exit(0);
}

$tcode = $_POST['tcode'];

// Check anti-spam sum

include 'php/checksum.php';

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
	$entrant = new Entrant($_POST["name"]);
	try  {
		$entrant->fetchdets($tourn);
		$isupd = true;
	}
	catch  (Tcerror $e)  {
		$isupd = false;
	}
	$entrant->frompost();
	$entrant->Fee = $entrant->total_fee($tourn);
	if (!preg_match('/@/', $entrant->Email))  {
		$play = new Player($entrant);
		if ($play->fetchplayer())
			$entrant->Email = $play->Email;
	}
	if  ($isupd)
		$entrant->update($tourn);
	else
		$entrant->create($tourn);
	$prog = $_SERVER['DOCUMENT_ROOT'] . '/acknow.pl';
	system("$prog \'$tcode\' \'{$entrant->First}\' \'{$entrant->Last}\'");
}
catch (Tcerror $e)  {
	$mess = $e->getMessage();
	include 'php/wrongentry.php';
	exit(0);
}

$Title = $isupd? "Amended entry accepted": "Entry accepted";
include 'php/head.php';
?>
<body>
<?php
include 'php/nav.php';
print <<<EOT
<h1>$Title</h1>
<p>Your entry to the {$tourn->display_name()} tournament has been accepted.</p>
<p>You should be receiving confirmation in your email shortly.</p>

Invoked $prog \'$tcode\' \'{$entrant->First}\' \'{$entrant->Last}\'

EOT;
?>
</div>
</div>
</body>
</html>
