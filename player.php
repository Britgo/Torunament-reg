<?php

class Player extends Person {
 	
 	public $Rank;
 	public $Club;
 	public $Country;
 	public $Email;
  	public $Nonbga;
 	
	public function __construct($f = "", $l = "", $rk = 0, $club = "", $cnt = "", $em = "", $nbg = false)  {
		if  (is_a($f, "Entrant"))  {
			parent::__construct($f->First, $f->Last);
			$this->Rank = new rank($f->Rank->Rankvalue);
			$this->Club = $f->Club;
			$this->Country = $f->Country;
 			$this->Email = $f->Email;
 			$this->Nonbga = $f->Nonbga;
		}
		else  {
			parent::__construct($f, $l);
			$this->Rank = new rank($rk);
			$this->Club = strlen($club) == 0? "No Club": $club;
			$this->Country = strlen($cnt) == 0? "UK": $cnt;
 			$this->Email = $em;
 			$this->Nonbga = $nbg;
 		}
 	}
 	
 	public function create_or_update()  {
 		$qq = $this->queryof();
 		$qfirst = mysql_real_escape_string($this->First);
		$qlast = mysql_real_escape_string($this->Last);
		$qclub = mysql_real_escape_string($this->Club);
		$qcountry = mysql_real_escape_string($this->Country);
		$qnonbga = $this->Nonbga? 1: 0;
		$ret = mysql_query("select first,email from player where $qq");
		if  (!$ret)  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot access player record, error was $ecode", "Database error");
		}
		if  (mysql_num_rows($ret) > 0)  {
			if (!preg_match('/@/', $this->Email))  {
				$row = mysql_fetch_array($ret);
				$this->Email = $row[1];
			}
			if  (!mysql_query("delete from player where $qq"))  {
				$ecode = mysql_error();
				throw  new  Tcerror("Cannot remove player record, error was $ecode", "Database error");
			}
		}
		$qemail = mysql_real_escape_string($this->Email);
		if  (!mysql_query("insert into player (first,last,rank,club,country,email,nonbga) values ('$qfirst','$qlast',{$this->Rank->Rankvalue},'$qclub','$qcountry','$qemail',$qnonbga)"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot create player record, error was $ecode", "Database error");
		}
	}
	
	public function display_country()
	{
		return htmlspecialchars($this->Country);
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
