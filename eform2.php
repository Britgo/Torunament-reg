<?php

/// Copyright John Collins 2014
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

include 'tcerror.php';
include 'tdate.php';
include 'rank.php';
include 'person.php';
include 'entrant.php';
include 'player.php';
include 'club.php';
include 'country.php';
include 'tournclass.php';
include 'opendb.php';

if (!isset($_POST['tcode']) || !isset($_POST['r1']) || !isset($_POST['r2']) || !isset($_POST['asp']))  {
print <<<EOT
<h1>Wrong entry</h1>
<p>I do not know how you got here, but it is wrong</p>

EOT;
	return;
}
$r1 = $_POST['r1'];
$r2 = $_POST['r2'];
$asp = strtolower($_POST['asp']);
$ans = array('zero','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty');
if  ($ans[$r1+$r2] != $asp) {
print <<<EOT
<h1>Bad sum</h1>
<p>Sorry but the answer to your sum was wrong, please try again - remember it should be figures, like "eleven".</p>

EOT;
	return;
}

$tcode = $_POST['tcode'];

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
	$player = new Player($entrant);
	$player->create_or_update();
	$club = new Club($player->Club, $player->Country);
	$country = new Country($player->Country);
	try  {
		$club->create();
		$country->create();
	}
	catch (Tcerror $e) {
		;
	}
	$prog = '/var/www/bgasite/tournreg/acknow.pl';
	system("$prog \'$tcode\' \'{$player->First}\' \'{$player->Last}\'");
}
catch (Tcerror $e)  {
	$hdr = $e->Header;
	$msg = htmlspecialchars($e->getMessage());
	print <<<EOT
<h1>$hdr</h1>
<p>$msg</p>

EOT;
	return;
}

header("Location: http://www.britgo.org/tournaments/_register/accepted?tcode=$tcode");
?>
