<?php

class Player extends Person {
 	
 	public $Rank;
 	public $Club;
 	public $Country;
 	public $Email;
  	public $Nonbga;
  	public $Admin;
 	
	public function __construct($f = "", $l = "", $rk = 0, $club = "", $cnt = "", $em = "", $nbg = false)  {
		if  (is_a($f, "Entrant"))  {
			parent::__construct($f->First, $f->Last);
			$this->Rank = new rank($f->Rank->Rankvalue);
			$this->Club = $f->Club;
			$this->Country = $f->Country;
 			$this->Email = $f->Email;
 			$this->Nonbga = $f->Nonbga;
 			$this->Admin = 'N';
		}
		else  {
			parent::__construct($f, $l);
			$this->Rank = new rank($rk);
			$this->Club = strlen($club) == 0? "No Club": $club;
			$this->Country = strlen($cnt) == 0? "UK": $cnt;
 			$this->Email = $em;
 			$this->Nonbga = $nbg;
 			$this->Admin = 'N';
 		}
 	}
 	
 	public function fetchplayer() {
 		$ret = mysql_query("select rank,club,country,email,nonbga.admin from player where {$this->queryof()}");
 		if (!$ret || mysql_num_rows($ret) == 0)
 			return  false;
 		$row = mysql_fetch_assoc($ret);
 		$this->Rank = new rank($row['rank']);
 		$this->Club = $row['club'];
 		$this->Country = $row['country'];
 		$this->Email = $row['email'];
 		$this->Nonbga = $row['nonbga'];
 		$this->Admin = $row["admin"];
 		return true;
 	}
 	
 	// Use me to get details starting from userid
	
	public function fromid($id) {
		$qid = mysql_real_escape_string($id);
		$ret = mysql_query("select first,last,rank,club,country,email,nonbga,admin from player where user='$qid'");
		if (!$ret || mysql_num_rows($ret) == 0)
			throw new Tcerror("Unknown player userid $id", "Userid not found");
		$row = mysql_fetch_assoc($ret);
		$this->First = $row['first'];
		$this->Last = $row['last'];
		$this->Rank = new Rank($row["rank"]);
		$this->Club = $row["club"];
		$this->Country = $row["country"];
		$this->Email = $row["email"];
		$this->Admin = $row["admin"];
		$this->Nonbga = $row["nonbga"];
	}
	
	// Use me to get the player we are talking about from a hidden field
	// We'll still perhaps need to get the rest
	
	public function frompost($prefix = "") {
		$this->First = $_POST["${prefix}f"];
		$this->Last = $_POST["${prefix}l"];
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new Tcerror("Null post name field"); 
	}
	
	// Are we talking about same player
		
	public function is_same($pl) {
		return $this->First == $pl->First && $this->Last == $pl->Last;
	}
 	
 	public function create()  {
 		$qq = $this->queryof();
 		$qfirst = mysql_real_escape_string($this->First);
		$qlast = mysql_real_escape_string($this->Last);
		$qclub = mysql_real_escape_string($this->Club);
		$qcountry = mysql_real_escape_string($this->Country);
		$qemail = mysql_real_escape_string($this->Email);
		$qnonbga = $this->Nonbga? 1: 0;
		$qrank = $this->Rank->Rankvalue;
		mysql_query("delete from player where $qq");
		if  (!mysql_query("insert into player (first,last,rank,club,country,email,nonbga) values ('$qfirst','$qlast',$qrank,'$qclub','$qcountry','$qemail',$qnonbga)"))  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot create player record, error was $ecode", "Database error");
		}
	}
	
	public function updatename($newp) {
		$qfirst = mysql_real_escape_string($newp->First);
		$qlast = mysql_real_escape_string($newp->Last);
		mysql_query("update player set first='$qfirst',last='$qlast' where {$this->queryof()}");
		$this->First = $newp->First;
		$this->Last = $newp->Last;
	}
	
	public function update()  {
 		$qq = $this->queryof();
 		$qclub = mysql_real_escape_string($this->Club);
		$qcountry = mysql_real_escape_string($this->Country);
		$qemail = mysql_real_escape_string($this->Email);
		$qnonbga = $this->Nonbga? 1: 0;
		$qrank = $this->Rank->Rankvalue;
		$ret = mysql_query("update player set rank=$qrank,club='$qclub',country='$qcountry',email='$qemail',nonbga=$qnonbga where $qq");
	}	
	
	public function save_hidden($prefix = "") {
		$f = htmlspecialchars($this->First);
		$l = htmlspecialchars($this->Last);
		return "<input type=\"hidden\" name=\"${prefix}f\" value=\"$f\"><input type=\"hidden\" name=\"${prefix}l\" value=\"$l\">";
	}
	
	// Get password
		
	public function get_passwd() {
		$ret = mysql_query("select password from player where {$this->queryof()}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  "";
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}
	
	// Set password
		
	public function set_passwd($pw, $uid = NULL)  {
		$qpw = mysql_real_escape_string($pw);
		if (is_null($uid))
				$ret = mysql_query("update player set password='$qpw' where {$this->queryof()}");
		else {
			$quid = mysql_real_escape_string($uid);
			$ret = mysql_query("update player set password='$qpw',user='$quid' where {$this->queryof()}");
		}
		if (!$ret)  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot set password, error was $ecode", "Database error");
		}
	}
	
	public function set_admin($adm = 'N')  {
		$qad = mysql_real_escape_string($adm);
		if  (!mysql_query("update player set admin='$qad' where {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot set admin, error was $ecode", "Database error");
		}
	}
	
	// Display whole name
		
	public function display_name() {
		$f = $this->First;
		$l = $this->Last;
		return  htmlspecialchars("$f $l");
	}
	
	//public function display_userid($wminus=1) {
	//	if ($wminus && strlen($this->Userid) == 0)
	//		return "-";
	//	return htmlspecialchars($this->Userid);
	//}
	
	public function display_country()
	{
		return htmlspecialchars($this->Country);
	}
	
	// Get password for "display"
		
	public function disp_passwd() {
		return htmlspecialchars($this->get_passwd());
	}
	
	public function display_email_nolink() {
		return htmlspecialchars($this->Email);
	}
	
	public function clubopt($selfn = "") {
		$clubs = list_clubs();
		$onc = "";
		if (strlen($selfn) != 0)
			$onc = " onchange=\"$selfn();\"";
		print "<select name=\"clubsel\"$onc>\n";
		print "<option value='none'>No club Selected</option>\n";
		foreach ($clubs as $club) {
			$name = $club->Name;
			$qname = htmlspecialchars($name);
			$v = $qname . ':' . htmlspecialchars($club->Country);
			if ($name == $this->Club)
				print "<option value=\"$v\" selected>$qname</option>\n";
			else
				print "<option value=\"$v\">$qname</option>\n";
		}
		print "</select>\n";
	}
	
	public function countryopt() {
		$countries = list_countries();
		print "<select name=\"countrysel\">\n";
		print "<option value='none'>None Selected</option>\n";
		foreach ($countries as $country) {
			$qname = htmlspecialchars($country->Name);
			if ($country->Name == $this->Country)
				print "<option value=\"$qname\" selected>$qname</option>\n";
			else
				print "<option value=\"$qname\">$qname</option>\n";
		}
		print "</select>\n";
	}	
	
	// Display rank as a selection
		
	public function rankopt($suff="") {
		$this->Rank->rankopt($suff);
	}
	
	public function bgaopt($isnew = TRUE, $selfn = "") {
		$onc = "";
		if (strlen($selfn) != 0)
			$onc = " onchange=\"$selfn());\"";
		$u = $m = $n = "";
		if ($isnew)
			$u = " selected";
		elseif ($this->Nonbga)
			$n = " selected";
		else
			$m = " selected";
		print <<<EOT
<select name="nonbga"$onc>
<option value="u"$u>Not selected</option>
<option value="m"$m>Member</option>
<option value="n"$n>Not member</option>
</select>

EOT;
		}
}

function list_players()
{
	$result = array();
	if  ($ret = mysql_query("select first,last,rank,club,country,email,nonbga from player order by first,last,rank desc,club"))  {
		while  ($row = mysql_fetch_array($ret))
			array_push($result, new Player($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]));
	}
	return $result;
}
?>
