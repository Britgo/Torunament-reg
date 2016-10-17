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

function newaccemail($email, $userid, $passw)  {
	if (strlen($email) != 0)  {
		$fh = popen("REPLYTO=admin@tournaments.britgo.org mail -s 'BGA tournament registration account created' $email", "w");
		fwrite($fh, "Please DO NOT reply to this message!!!\n\n");
		fwrite($fh, "A BGA tournament registration account has been created for you on http://tournaments.britgo.org\n\n");
		fwrite($fh, "Your user id is $userid and your password is $passw\n\n"); 
		fwrite($fh, "Please log in and reset your password if you wish\n");
		pclose($fh);
	}
}

function ackentry($tourn, $entrant, $upd)  {
	$orgname = "{$tourn->Contact->First} {$tourn->Contact->Last}";
	$entname = "{$entrant->First} {$entrant->Last}";
	$orgemail = $tourn->get_org_email();
	$entemail = $entrant->Email;
	if  (strlen($entemail) != 0)  {
		$fh = popen("REPLYTO=admin@tournaments.britgo.org mail -s 'Entry accepted' $entemail", "w");
		$mess = <<<EOT
Please DO NOT reply to this message!!!

Dear $entname,

Thank you for your entry for the {$tourn->Name} tournament,
which has been received.

If you have any questions about the tournament, please ask the organiser,
$orgname on $orgemail.

EOT;
		fwrite($fh, $mess);
		pclose($fh);
	}
	if ($upd)  {
		$fh = popen("REPLYTO=admin@tournaments.britgo.org mail -s 'Tournament entry amended' $orgemail", "w");
		$mess = <<<EOT
Please DO NOT reply to this message!!!

Dear $orgname,

$entname has amended his/her entry to the {$tourn->Name} tournament.

EOT;
	}
	else  {
		$fh = popen("REPLYTO=admin@tournaments.britgo.org mail -s 'Tournament entry accepted' $orgemail", "w");
		$mess = <<<EOT
Please DO NOT reply to this message!!!

Dear $orgname,

$entname has entered the {$tourn->Name} tournament.

EOT;
	}
	fwrite($fh, $mess);
	pclose($fh);
}
?>
