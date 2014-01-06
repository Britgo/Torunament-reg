<?php

include 'tcerror.php';
include 'tdate.php';
include 'person.php';
include 'tournclass.php';

function playerselect($players, $name, $jsfunc, $current)
{
	if  (count($players) == 0)
		return;
	print <<<EOT
	<select name="$name" onchange="$jsfunc();">
<option value="">(None)</option>

EOT;
	foreach  ($players as $p)  {
		$sel = "";
		if ($p->is_same($current))
			$sel = ' selected="selected"';
		print <<<EOT
<option$sel>{$p->display_name()}</option>

EOT;
	}
	print <<<EOT
</select>
<br />

EOT;
}

// Create form to create/update a tournament
// Pass it a tournament class object as parameter.
// Also pass player list to help select champ and contacts


function tcform($creating, $tourn, $playerlist)
{ 
	// Create Javascript function to check entries
 
	print <<<EOT
<script language="javascript">

EOT;
	
	//  If creating new one, set up list of existing codes to check against
	
	if  ($creating)  {
		print "Existing_codes = new Array();\n";
		$codes = get_tcodes();
		foreach ($codes as $code)
			print "Existing_codes['$code'] = 1;\n";
	}
	
	// Create Javascript check functions
	
	print <<<EOT
function champfn()
{
	var fm = document.ctform;
	var ps = fm.champsel;
	var psi = ps.selectedIndex;
	if  (psi < 0)
		return;
	fm.champ.value = ps.options[psi].value;
}
function contfn()
{
	var fm = document.ctform;
	var ps = fm.contsel;
	var psi = ps.selectedIndex;
	if  (psi < 0)
		return;
	fm.contact.value = ps.options[psi].value;
}
function okcode(s)  {
	return /^\w+\$/.test(s);
}
function nonblank(s)  {
	return /\S/.test(s);
}
function isprice(s, descr)  {
	var  v = s.value;
	if  (!/^\d+\.\d\d\$/.test(v))
		throw Error("Invalid price value for " + descr);
	return  parseFloat(s.value);
}
function getsel(el, descr) {
	var si = el.selectedIndex;
	if  (si < 0)
		throw Error("No " + descr + " selected");
	return el.options[si].value;
}
function datecheck(fmyr, fmmon, fmdy, descr)
{
   	var ds = getsel(fmdy, "Day for " + descr);
    	var ms = getsel(fmmon, "Month for " + descr);
    	var ys = getsel(fmyr, "Year for " + descr);
    	var tdat = new Date(ys, ms-1, ds, 12, 0, 0);
   	var now = new Date();
   	if  (tdat < now)
     	throw Error("Time for " + descr + " has to be in future");
  	if (tdat.getMonth() != ms-1)
  		throw Error("Invalid date for " + descr);
	return tdat;
}
function checkform() {
	var fm = document.ctform;
   
EOT;
   
	if  ($creating)  {
		print <<<EOT
	var tcode = fm.tcode.value;
   	if  (!nonblank(tcode))  {
     	alert("Please give a tournament code (e.g. anytown2014)");
      	return false;
   	}
	if  (!okcode(tcode))  {
		alert("Please give a meaningful tournament code letters/digits");
		return false;
	}
	if (Existing_codes[tcode]) {
     	alert("Already have a tournament called " + tcode);
      	return false;
   	}

EOT;
	}	// Back in PHP....
	
	//  Insert code to do balance of form-checking
	
	print <<<EOT
   	if  (!nonblank(fm.tname.value))  {
		alert("Please give a tournament name");
		return false;
	}
     if (!nonblank(fm.address.value)) {
	   	alert("Please give a venue");
     	 return false;
   	}
   	if (!nonblank(fm.postcode.value))  {
		alert("Please give a postcode");
		return  false;
   	}
   	if (!nonblank(fm.contact.value)) {
   		alert("Please give contact name");
   		return  false;
   	}
   	if (!nonblank(fm.email.value)) {
   		alert("Please give contact email");
   		return  false;
   	}
   	try {
   		if  (isprice(fm.fee, "fee") <= 3.0)
   			throw Error("Invalid fee - too low");	
   		isprice(fm.lunch, "lunch");
   		isprice(fm.nonbga, "non-BGA");
		isprice(fm.concess1, "Concession 1");
		isprice(fm.concess2, "Concession 2");
   		isprice(fm.latefee, "late fee");
   		var tdat = datecheck(fm.year, fm.month, fm.day, "Start date");
   		if  (isprice(fm.ebird, "Early bird") != 0.0)  {
   			var ebdat = datecheck(fm.ebyear, fm.ebmonth, fm.ebday, "Early Bird date");
   			if  (ebdat > tdat - 604800000)
   				throw Error("Early bird date should be earlier!");
   		}
 		if (tdat.getDay() > 0  &&  tdat.getDay() < 5  &&  !confirm("Not a weekend - OK"))
			return false;
	}
	catch (err)  {
		alert(err.message);
		return false;
	}
	return true;
}
</script>

EOT;
	if  ($creating)  {
		// Possibly make up a new code if cloning
		$dat = getdate();
		$year = $dat['year'];
		if  ($tourn->isnew())  {
			$tcd = "EDITME$year";
		}
		elseif (preg_match('/20\d\d/', $tourn->Tcode))  {
			$tcd = preg_replace('/20\d\d/', $year, $tourn->Tcode);
		}
		else {
			$tcd = $tourn->Tcode . $year;
		}
		print <<<EOT
<form name="ctform" action="/phpsegments/ct2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkform();">
<table summary="Tournament Table">
<tr>
	<td colspan="2">Please enter a unique single-word code for the tournament here.<br />This should be letters and digits only.</td>
</tr>
<tr>
	<td>Tournament Code</td>
	<td><input type="text" name="tcode" size="20" value="$tcd"></td>
</tr>

EOT;
	}
	else  {
		print  <<<EOT
<form name="ctform" action="/phpsegments/ut2.php" method="post" enctype="application/x-www-form-urlencoded" onsubmit="javascript:return checkform();">
<table summary="Tournament Table">

EOT;
		$tourn->set_hidden();		
	}
	
	// Set checkboxes for current settings of provisional and open
	
	$pprov = $tourn->Provisional? " checked=\"checked\"": "";
	$popen = $tourn->Open? " checked=\"checked\"": "";
	$ach = $bch = $cch = $nch = "";
	switch ($tourn->Tclass) {
	case 'A':
		$ach = ' checked="checked"';
		break;
	case 'B':
		$bch = ' checked="checked"';
		break;
	case 'C':
		$cch = ' checked="checked"';
		break;
	default:
		$nch = ' checked="checked"';
		break;
	}
		
	print <<<EOT
<tr>
	<td colspan="2">Please give a full nuame for the tournament, usually just location and year.<br />
	This is normally displayed as a heading in various places.</td>
</tr>
<tr>
	<td>Tournament Name</td>
	<td><input type="text" name="tname" value="{$tourn->display_name()}" size="40"></td>
</tr>
<tr>
	<td colspan="2">This is the class of the tournament, A B or C. If this is not a class which
	affects ratings, select N.</td>
</tr>
<tr>
	<td>Tournament Class</td>
	<td>
		<input type="radio" name="tclass" value="A"$ach />A
		<input type="radio" name="tclass" value="B"$bch />B
		<input type="radio" name="tclass" value="C"$cch />C
		<input type="radio" name="tclass" value="N"$nch />N
	</td>
</tr>
<tr>
	<td>Format<br />Please give a brief description of the tournament parameters with time limits.
	Say what the rules are if not AGA and 7.5 Komi.</td>
	<td><textarea name="format" rows="6" cols="50">{$tourn->display_format()}</textarea></td>
</tr>
<tr>
	<td>Overview<br />Please give a brief description of the tournament timetable. Mention any side
	tournaments e.g. 13x13, quiz etc.</td>
	<td><textarea name="overview" rows="6" cols="50">{$tourn->display_over()}</textarea></td>
</tr>
<tr>
	<td>Venue<br />Please give a description of the venue, with street address.</td>
	<td><textarea name="address" rows="6" cols="50">{$tourn->display_addr()}</textarea></td>
</tr>
<tr>
	<td colspan="2">Please give the correct postcode of the address for the benefit of Sat Nav users.</td>
</tr>
<tr>
	<td>Postcode</td>
	<td><input type="text" name="postcode" size="10" value="{$tourn->display_pc()}"></td>
</tr>
<tr>
	<td>Provisional dates and details</td>
	<td><input type="checkbox" name="provisional"$pprov /></td>
</tr>
<tr>
	<td>Open for entries</td>
	<td><input type="checkbox" name="open"$popen /></td>
</tr>
	<td>Date (or first day)</td><td>
EOT;
	$tourn->Sdate->dateopt();
	print <<<EOT
</td></tr>
<tr>
	<td>Number of days</td>
	<td><select name="ndays">
EOT;
	for ($i = 1;  $i <= 7;  $i++)
		if  ($i == $tourn->Ndays)
			print "<option selected=\"selected\">$i</option>\n";
		else
			print "<option>$i</option>\n";

	print <<<EOT
</select></td>
</tr>
<tr>
	<td>Total number of rounds</td>
	<td><select name="rounds">

EOT;

	for ($i = 3;  $i <= 20;  $i++)
		if  ($i == $tourn->Nrounds)
			print "<option selected=\"selected\">$i</option>\n";
		else
			print "<option>$i</option>\n";
	print <<<EOT
</select></td>
</tr>
<tr>
	<td>Basic fee for tournament</td>
	<td><input type="text" name="fee" value="{$tourn->display_fee()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td>Supplement for lunch, put zero if no lunch</td>
	<td><input type="text" name="lunch" value="{$tourn->display_lunch()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td>Supplement if not BGA member</td>
	<td><input type="text" name="nonbga" value="{$tourn->display_nonbga()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td colspan="2">The following two fields are concession amounts.
	Typically entrants are either standard, or concessionary if retired or students.
	This allows up to two alternative concession discounts, plus descriptions to be assigned.
	Just set one to zero to turn it off.</td>
<tr>
	<td>Concession 1</td>
	<td><input type="text" name="concess1" value="{$tourn->display_concess1()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td>Concession 1 description</td>
	<td><input type="text" name="concess1name" value="{$tourn->display_concess1name()}" size="20" /></td>
</tr>
<tr>
	<td>Concession 2</td>
	<td><input type="text" name="concess2" value="{$tourn->display_concess2()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td>Concession 2 description</td>
	<td><input type="text" name="concess2name" value="{$tourn->display_concess2name()}" size="20" /></td>
</tr>
<tr>
	<td>Late entry fee</td>
	<td><input type="text" name="latefee" value="{$tourn->display_latefee()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td>Late entry applies if booked</td>
	<td><select name="latedays">

EOT;
	for ($i = 0;  $i <= 10;  $i++)  {
		$lab = "$i days before";
		if  ($i == 0)
			$lab = "On day";
		elseif  ($i == 1)
			$lab = "Previous day";
		if  ($i == $tourn->Latedays)
			print "<option value=\"$i\" selected=\"selected\">$lab</option>\n";
		else
			print "<option value=\"$i\">$lab</option>\n";
	}
	print <<<EOT
</select></td>
</tr>
<tr>
	<td>Early bird discount</td>
	<td><input type="text" name="ebird" value="{$tourn->display_ebird()}" size="6" maxlength="6" /></td>
</tr>
<tr>
	<td>Last day for early bird</td>
	<td>
EOT;
	$tourn->Ebdate->dateopt("eb");
	print <<<EOT
</td></tr>
<tr>
	<td>Current Champion</td>
	<td>
EOT;
	playerselect($playerlist, "champsel", "champfn", $tourn->Champion);
	print <<<EOT
	<input type="text" name="champ" value="{$tourn->Champion->display_name()}" size="30"></td>
</tr>
<tr>
	<td>Contact</td>
	<td>
EOT;
	playerselect($playerlist, "contsel", "contfn", $tourn->Contact);
	print <<<EOT
	<input type="text" name="contact" value="{$tourn->Contact->display_name()}" size="30"></td>
</tr>
<tr>
	<td>Contact email</td>
	<td><input type="text" name="email" value="{$tourn->display_email()}" size="30"></td>
</tr>
<tr>
	<td>Website</td>
	<td><input type="text" name="website" value="{$tourn->display_ws()}" size="40"></td>
</tr>

EOT;
	if  ($creating)
		print <<<EOT
<tr>
	<td>Click to create</td>
	<td><input type="submit" value="Create tournament"></td>
</tr>

EOT;
	else
		print <<<EOT
<tr>
	<td>Click to update</td>
	<td><input type="submit" value="Update tournament"></td>
</tr>

EOT;
	print <<<EOT
</table>
</form>

EOT;
}
?>
