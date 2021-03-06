<?php

class Player extends Person {
 	
 	public $Rank;
 	public $Club;
 	public $Country;
 	public $Email;
  	public $Nonbga;
  	public $Login;
  	public $Admin;
 	
	public function __construct($f = "", $l = "", $rk = 0, $club = "", $cnt = "", $em = "", $nbg = false, $log = "", $adm = 'N')  {
		if  (is_a($f, "Entrant"))  {
			parent::__construct($f->First, $f->Last);
			$this->Rank = new rank($f->Rank->Rankvalue);
			$this->Club = $f->Club;
			$this->Country = $f->Country;
 			$this->Email = $f->Email;
 			$this->Nonbga = $f->Nonbga;
 			$this->Login = $log;
 			$this->Admin = $adm;
		}
		else  {
			parent::__construct($f, $l);
			$this->Rank = new rank($rk);
			$this->Club = strlen($club) == 0? "No Club": $club;
			$this->Country = strlen($cnt) == 0? "UK": $cnt;
 			$this->Email = $em;
 			$this->Nonbga = $nbg;
 			$this->Login = $log;
 			$this->Admin = $adm;
 		}
 	}
 	
 	public function fetchplayer() {
 		$ret = mysql_query("select rank,club,country,email,nonbga,user,admin from player where {$this->queryof()}");
 		if (!$ret)
 			throw new Tcerror(mysql_error(), "Read player error");
 		if (mysql_num_rows($ret) == 0)
 			return  false;
 		$row = mysql_fetch_assoc($ret);
 		$this->Rank = new rank($row['rank']);
 		$this->Club = $row['club'];
 		$this->Country = $row['country'];
 		$this->Email = $row['email'];
 		$this->Nonbga = $row['nonbga'];
 		$this->Login = $row['user'];
 		$this->Admin = $row["admin"];
 		return true;
 	}
 	
 	// Use me to get details starting from userid
	
	public function fromid($id) {
		$qid = mysql_real_escape_string($id);
		$ret = mysql_query("select first,last,rank,club,country,email,nonbga,user,admin from player where user='$qid'");
		if (!$ret)
 			throw new Tcerror(mysql_error(), "Read player error");
		if (mysql_num_rows($ret) == 0)
			throw new Tcerror("Unknown player userid $id", "Userid not found");
		$row = mysql_fetch_assoc($ret);
		$this->First = $row['first'];
		$this->Last = $row['last'];
		$this->Rank = new Rank($row["rank"]);
		$this->Club = $row["club"];
		$this->Country = $row["country"];
		$this->Email = $row["email"];
		$this->Login = $row['user'];
		$this->Admin = $row["admin"];
		$this->Nonbga = $row["nonbga"];
		return $this;
	}
	
	public function fromget($prefix = "") {
		$this->First = $_GET["${prefix}f"];
		$this->Last = $_GET["${prefix}l"];
		if (strlen($this->First) == 0 || strlen($this->Last) == 0)
			throw new Tcerror("Null name field"); 
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
		return strcasecmp($this->First, $pl->First) == 0 && strcasecmp($this->Last, $pl->Last) == 0;
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
		if  (!mysql_query("INSERT INTO player (first,last,rank,club,country,email,nonbga) VALUES ('$qfirst','$qlast',$qrank,'$qclub','$qcountry','$qemail',$qnonbga)"))  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot create player record, error was $ecode", "Database error");
		}
	}
	
	public function updatename($newp) {
		$qfirst = mysql_real_escape_string($newp->First);
		$qlast = mysql_real_escape_string($newp->Last);
		mysql_query("UPDATE player SET first='$qfirst',last='$qlast' WHERE {$this->queryof()}");
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
		$ret = mysql_query("UPDATE player SET rank=$qrank,club='$qclub',country='$qcountry',email='$qemail',nonbga=$qnonbga WHERE $qq");
		if (!$ret)  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot update player, error was $ecode", "Database error");
		}
	}
	
	public function delete_player() {
		$ret = mysql_query("DELETE FROM player WHERE {$this->queryof()}");
		if (!$ret)  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot delete player, error was $ecode", "Database error");
		}
	}
	
	public function save_hidden($prefix = "") {
		$f = htmlspecialchars($this->First);
		$l = htmlspecialchars($this->Last);
		return "<input type=\"hidden\" name=\"${prefix}f\" value=\"$f\"><input type=\"hidden\" name=\"${prefix}l\" value=\"$l\">";
	}
	
	// Get userid
	
	public function get_userid() {
		$ret = mysql_query("SELECT user FROM player WHERE {$this->queryof()}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  "";
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}
	
	// Get password
		
	public function get_passwd() {
		$ret = mysql_query("SELECT password FROM player WHERE {$this->queryof()}");
		if (!$ret || mysql_num_rows($ret) == 0)
			return  "";
		$row = mysql_fetch_array($ret);
		return $row[0];	
	}
	
	// Set password
		
	public function set_passwd($pw, $uid = NULL)  {
		$qpw = mysql_real_escape_string($pw);
		if (is_null($uid))
				$ret = mysql_query("UPDATE player SET password='$qpw' WHERE {$this->queryof()}");
		else {
			$quid = mysql_real_escape_string($uid);
			$ret = mysql_query("UPDATE player SET password='$qpw',user='$quid' WHERE {$this->queryof()}");
		}
		if (!$ret)  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot set password, error was $ecode", "Database error");
		}
	}
	
	public function set_admin($adm = 'N')  {
		$qad = mysql_real_escape_string($adm);
		if  (!mysql_query("UPDATE player SET admin='$qad' WHERE {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot set admin, error was $ecode", "Database error");
		}
	}
	
 	public function display_rank() {
 		return $this->Rank->display();
 	}
 	public function display_club() {
 		return  htmlspecialchars($this->Club);
 	}
 	public function display_country() {
 		return  htmlspecialchars($this->Country);
 	}
 	public function display_login() {
 		return  htmlspecialchars($this->Login);
 	}
 	public function display_admin() {
 		switch ($this->Admin) {
 			default: return "Normal";
 			case 'O': return "Org";
 			case 'A': return "Admin";
 			case 'SA': return "Super";
 		}
 	}

	// Get password for "display"
		
	public function disp_passwd() {
		return htmlspecialchars($this->get_passwd());
	}
	
	public function display_email_nolink() {
		return htmlspecialchars($this->Email);
	}
	
	public function clubopt($selfn = "") {
		$clubs = Club::list_clubs();
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
	
	public function countryopt($selfn = "") {
		Country::countryopt($this->Country, $selfn);
	}	
	
	// Display rank as a selection
		
	public function rankopt($suff="") {
		$this->Rank->rankopt($suff);
	}
	
	public function bgaopt($isnew = TRUE, $selfn = "") {
		$onc = "";
		if (strlen($selfn) != 0)
			$onc = " onchange=\"$selfn();\"";
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
<option value="m"$m>Member (or national org)</option>
<option value="n"$n>Not member</option>
</select>

EOT;
		}
		
	public static function list_players()  {
		$result = array();
		if ($ret = mysql_query("SELECT first,last,rank,club,country,email,nonbga,user,admin FROM player ORDER BY last,first,rank desc,club"))  {
			while ($row = mysql_fetch_array($ret))
				array_push($result, new Player($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8]));
		}
		return $result;
	}

	public static function check_clash_userid($uid)  {
		$quid = mysql_real_escape_string($uid);
		$ret = mysql_query("SELECT COUNT(*) FROM player WHERE user='$quid'");
		if  (!$ret)
			throw new Tcerror(mysql_error(), "Database error");
		$row = mysql_fetch_array($ret);
		return $row[0] != 0;
	}
}
?>
