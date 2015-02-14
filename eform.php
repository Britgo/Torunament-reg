<?php

// Copyright John Collins 2014
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

include 'tcerror.php';
include 'tdate.php';
include 'rank.php';
include 'person.php';
include 'tournclass.php';
include 'opendb.php';
include 'player.php';
include 'club.php';
include 'country.php';

if (!isset($_GET['tcode']))  {
print <<<EOT
<h1>Wrong entry</h1>
<p>I do not know how you got here, but it is wrong</p>

EOT;
	return;
}

$tcode = $_GET['tcode'];

try  {
	opendb();
	$tourn = new Tournament($tcode);
	$tourn->fetchdets();
	$players = list_players();
	$clubs = list_clubs();
	$countries = list_countries();
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

print <<<EOT
<script language="javascript">

function nonblank(s)  {
        return /\S/.test(s);
}

function okname(s)  {
	return /^[A-Z][a-z]*( +(Mc|O')?[A-Z][a-z]*(-[A-Z][a-z]*)?)+$/.test(s); //'
}

function checkform() {
	 var fm = document.entryform;
    if  (!nonblank(fm.name.value))  {
    	  alert("Please give your name");
        return false;
    }
    if  (!okname(fm.name.value))  {
    	  alert("Sorry unacceptable name format");
        return false;
    }
    if (!nonblank(fm.club.value)) {
    	  alert("Please give your club - or put 'No club'");
        return false;
    }
    if (!nonblank(fm.country.value))  {
    	  alert("Please give a country");
   	  return  false; 	  
    }
	 if (!nonblank(fm.email.value))  {
		  alert("Please give your email address");
		  return  false;
	 }  
    return true;
}

function calccost() {
	var fm = document.entryform;
	var cost = {$tourn->display_basic_fee()};

EOT;

// Only put in code for non-bga if we have a charge for it
// (provides for candidates where we don't allow such folk in)

if ($tourn->Nonbga != 0)
	print <<<EOT
	if (fm.nonbga.checked)
		cost += {$tourn->display_nonbga()};

EOT;

	// Process concession radio boxes but only if we have one.
	// This is a horrible interleaving of PHP and JavaScript....
	// Enjoy!
	// If there are any concessions there will be a radio button set

if  ($tourn->Concess1 != 0  ||  $tourn->Concess2 != 0)  {		// Don't bother if none
	print <<<EOT
	var radios = document.getElementsByName("concess");
	var i;
	var chkd = "std";
	for (i in radios)
		if (radios[i].checked)
			chkd = radios[i].value;

EOT;

	// If we have concession 1 insert code to check that radio button got set
		
	if  ($tourn->Concess1 != 0)
		print <<<EOT
	if (chkd == "C1")
		cost -= {$tourn->display_concess1()};

EOT;

	// Ditto concession 2

	if  ($tourn->Concess2 != 0)
		print <<<EOT
	if (chkd == "C2")
		cost -= {$tourn->display_concess2()};

EOT;
}

//  If there is a lunch insert code to calculate it
		
if  ($tourn->Lunch > 0)
	print <<<EOT
	if (fm.lunch.checked)
		cost += {$tourn->display_lunch()};

EOT;

print <<<EOT
	costp = Math.floor(cost);
	costd = Math.floor((cost - costp) * 100) + 100;
	costd += '';
	fm.cost.value = costp + '.' + costd.substr(1);
}

EOT;

if  (count($players) > 0)  {
	print <<<EOT
function playersel() {
	var fm = document.entryform;
	var ps = fm.psel;
	var psi = ps.selectedIndex;
	if  (psi < 0)
		return;
	var optv = ps.options[psi].value;
	var parts = optv.split(':');
	fm.name.value = parts[0] + ' ' + parts[1];
	fm.country.value = "UK";
	fm.club.value = parts[2];
	if (nonblank(parts[4]))
		fm.email.value = "(as before)";
	else
		fm.email.value = "";
	var rnk = parseInt(parts[3]);
	fm.rank.selectedIndex = 8 - rnk;
	fm.nonbga.checked = parseInt(parts[5]) != 0;
	calccost();
}

EOT;
}
if (count($clubs) > 0)
	print <<<EOT
function club_sel() {
	var fm = document.entryform;
	var ps = fm.clubsel;
	var psi = ps.selectedIndex;
	if  (psi < 0)
		return;
	var optv = ps.options[psi].value;
	var parts = optv.split(':');
	fm.club.value = parts[0];
	fm.country.value = parts[1]; 
}

EOT;
if (count($countries) > 0)
	print <<<EOT
function country_sel() {
	var fm = document.entryform;
	var ps = fm.countrysel;
	var psi = ps.selectedIndex;
	if  (psi < 0)
		return;
	fm.country.value = ps.options[psi].value;
}

EOT;
print <<<EOT
</script>
<h2>Entry form for: {$tourn->display_name()}</h2>
<p>{$tourn->html_format()}</p>
<p>{$tourn->html_over()}</p>
EOT;

if ($tourn->Provisional)
	print <<<EOT
<p><b>Please note that this tournament is <u>provisional</u>. dates, arrangements and fees may be subject to change.</b></p>

EOT;
print <<<EOT
<form name="entryform" action="/tournreg/eform2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkform();">
<input type="hidden" name="tcode" value="$tcode" />
<table>

EOT;
if  (count($players) > 0)  {
	print <<<EOT
<tr>
	<td rowspan="2">Player Name</td>
	<td><select name="psel" onchange="playersel();">
<option selected="selected" value="::None:0:">(None)</option>

EOT;
	foreach  ($players as $p)  {
		$f = htmlspecialchars($p->First);
		$l = htmlspecialchars($p->Last);
		$c = htmlspecialchars($p->Club);
		$e = htmlspecialchars($p->Email);
		$n = $p->Nonbga? 1: 0;
		$r = $p->Rank->Rankvalue;
		print <<<EOT
<option value="$f:$l:$c:$r:$e:$n">{$p->display_name()}</option>

EOT;
	}
	print <<<EOT
</select>
</td></tr>
<tr>
	<td><input type="text" name="name" size="30"></td>
</tr>

EOT;
}
else
	print <<<EOT
<tr>
	<td>Player Name</td>
	<td><input type="text" name="name" size="30"></td>
</tr>

EOT;
if (count($clubs) > 0) {
		print <<<EOT
<tr>
	<td rowspan="2">Club (select if you can)</td>
	<td><select name="clubsel" onchange="club_sel();">
<option selected="selected" value=":">(None)</option>

EOT;
	foreach  ($clubs as $club)  {
		$cl = htmlspecialchars($club->Name);
		$cnt = htmlspecialchars($club->Country);
		print <<<EOT
<option value="$cl:$cnt">$cl</option>

EOT;
    }
	print <<<EOT
</select>
</td></tr>
<tr>
	<td><input type="text" name="club" size="25"></td>
</tr>

EOT;
}
else
	print <<<EOT
<tr>
	<td>Club</td>
	<td><input type="text" name="club" size="25"></td>
</tr>

EOT;
if (count($countries) > 0) {
		print <<<EOT
<tr>
	<td rowspan="2">Country</td>
	<td><select name="countrysel" onchange="country_sel();">
<option selected="selected" value="">(None)</option>

EOT;
	foreach  ($countries as $country)  {
		$cnt = htmlspecialchars($country->Name);
		print <<<EOT
<option value="$cnt">$cnt</option>

EOT;
    }
	print <<<EOT
</select>
</td></tr>
<tr>
	<td><input type="text" name="country" size="20"></td>
</tr>

EOT;
}
else
	print <<<EOT
<tr>
	<td>Club</td>
	<td><input type="text" name="country" size="20"></td>
</tr>

EOT;
print <<<EOT
<tr>
	<td>Grade</td>
<td>
EOT;
$r = new rank();
$r->rankopt();
print <<<EOT
</td>
</tr>
<tr>
	<td>Email</td>
	<td><input type="text" name="email" size="30"></td>
</tr>

EOT;

if ($tourn->Nonbga != 0)
	print <<<EOT
<tr>
	<td>Not BGA Member (add &pound;{$tourn->display_nonbga()})</td>
	<td><input type="checkbox" name="nonbga" onchange="calccost();"></td>
</tr>

EOT;
if  ($tourn->Concess1 != 0  || $tourn->Concess2 != 0)  {
	$rows = 2;
	if  ($tourn->Concess1 != 0  && $tourn->Concess2 != 0)
		$rows = 3;
	print <<<EOT
<tr>
	<td rowspan="$rows">Entry Type</td>
	<td><input type="radio" name="concess" value="std" checked="checked" onchange="calccost();" />Standard</td>
</tr>

EOT;
	if ($tourn->Concess1 != 0)
		print <<<EOT
<tr>
	<td><input type="radio" name="concess" value="C1" onchange="calccost();" />{$tourn->display_concess1name()}</td>
</tr>

EOT;
	if ($tourn->Concess2 != 0)
		print <<<EOT
<tr>
	<td><input type="radio" name="concess" value="C2" onchange="calccost();" />{$tourn->display_concess2name()}</td>
</tr>

EOT;
}
if  ($tourn->Lunch > 0)
	print <<<EOT
<tr>
	<td>Lunch (add &pound;{$tourn->display_lunch()})</td>
	<td><input type="checkbox" name="lunch" onchange="calccost();"></td>
</tr>

EOT;

if (strlen($tourn->Dinner) != 0)
	print <<<EOT
<tr>
	<td>Joining for {$tourn->display_dinner()}</td>
	<td><input type="checkbox" name="dinner"></td>
</tr>

EOT;

$r1 = rand(1,10);
$r2 = rand(1,10);

print <<<EOT
<tr>
	<td>Don't list publicly</td>
	<td><input type="checkbox" name="privacy"></td>
</tr>
<tr>
	<td>Cost</td>
	<td>&pound;<input name="cost" type="text" size="5" maxlength="5" value="{$tourn->display_basic_fee()}"></td>
</tr>
<tr>
	<td>(Anti-spam) Please answer this sum as a <b>word</b> $r1 + $r2 =</td>
	<td><input type="hidden" name="r1" value="$r1" /><input type="hidden" name="r2" value="$r2" /><input type="text" name="asp" size="20" /></td>
</tr>
<tr>
	<td>Click to enter</td>
	<td><input type="submit" value="Submit"></td>
</tr>
</table>
</form>

EOT;
?>
