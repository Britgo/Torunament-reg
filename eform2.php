<?php

// Set up everything from functions

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

if (!isset($_POST['tcode']))  {
print <<<EOT
<h1>Wrong entry</h1>
<p>I do not know how you got here, but it is wrong</p>

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

header("Location: http://www.stalbans-go.org.uk/tentryok?tcode=$tcode");
?>
