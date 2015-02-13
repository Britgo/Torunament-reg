<?php

class Entrant extends Person {
 	public $Rank;
 	public $Club;
 	public $Country;
 	public $Nonbga;
 	public $Concess1;
 	public $Concess2;
 	public $Fee;
 	public $Edate;
 	public $Lunch;
 	public $Dinner;
 	public $Email;
 	public $Privacy;
 	
	public function __construct($f = "", $l = "") {
		parent::__construct($f, $l);
		$this->Rank = new rank();
 		$this->Club = "";
 		$this->Country = "UK";
 		$this->Nonbga = false;
 		$this->Concess1 = false;
 		$this->Concess2 = false;
 		$this->Fee - 0.0;
 		$this->Edate = new Tdate();
 		$this->Lunch = false;
 		$this->Dinner = false;
 		$this->Email = "";
 		$this->Privacy = false;
 	}
 		
 	public function fetchdets($t)  {
 		$ret = mysql_query("select club,country,email,rank,nonbga,lunch,dinner,privacy,concess1,concess2,edate,fee from {$t->etable()} where {$this->queryof()}");
 		if  (!$ret)
 			throw new Tcerror("Cannot read entrant details", "Fetch entrant details error");
 		if  (mysql_num_rows($ret) == 0)
 			throw new Tcerror("No entrant found {$this->First} {$this->Last}". "Read fail");
 		$row = mysql_fetch_assoc($ret);
 		$this->Rank = new rank($row['rank']);
 		$this->Club = $row['club'];
 		$this->Country = $row['country'];
 		$this->Email = $row['email'];
 		$this->Nonbga = $row['nonbga'];
 		$this->Lunch = $row['lunch'];
 		$this->Dinner = $row['dinner'];
 		$this->Privacy = $row['privacy'];
 		$this->Concess1 = $row['concess1'];
 		$this->Concess2 = $row['concess2'];
 		$this->Fee = $row['fee'];
 		$this->Edate->enctime($row['edate']);	
 	}
 		
	public function create($t)	
 	{
		$qfirst = mysql_real_escape_string($this->First);
		$qlast = mysql_real_escape_string($this->Last);
		$qclub = mysql_real_escape_string($this->Club);
		$qcountry = mysql_real_escape_string($this->Country);
		$qemail = mysql_real_escape_string($this->Email);
		$qlunch = $this->Lunch? 1: 0;
		$qdinner = $this->Dinner? 1: 0;
		$qnonbga = $this->Nonbga? 1: 0;
		$qprivacy = $this->Privacy? 1: 0;
		$qconcess1 = $this->Concess1? 1: 0;
		$qconcess2 = $this->Concess2? 1: 0;
		$qdat = $this->Edate->queryof();
		if  (!mysql_query("insert into {$t->etable()} (first,last,club,country,email,edate,rank,nonbga,lunch,dinner,privacy,concess1,concess2,fee) values ('$qfirst','$qlast','$qclub','$qcountry','$qemail','$qdat',{$this->Rank->Rankvalue},$qnonbga,$qlunch,$qdinner,$qprivacy,$qconcess1,$qconcess2,$this->Fee)"))  {
			$ecode = mysql_error();
			throw new Tcerror("Cannot create entrant record, error was $ecode", "Database error");
		}
	}
	
	public function update($t)
	{
		$qclub = mysql_real_escape_string($this->Club);
		$qcountry = mysql_real_escape_string($this->Country);
		$qemail = mysql_real_escape_string($this->Email);
		$qdat = $this->Edate->queryof();
		$qlunch = $this->Lunch? 1: 0;
		$qdinner = $this->Dinner? 1: 0;
		$qnonbga = $this->Nonbga? 1: 0;
		$qprivacy = $this->Privacy? 1: 0;
		$qconcess1 = $this->Concess1? 1: 0;
		$qconcess2 = $this->Concess2? 1: 0;
		if  (!mysql_query("update {$t->etable()} set club='$qclub',country='$qcountry',email='$qemail',edate='$qdat',rank={$this->Rank->Rankvalue},nonbga=$qnonbga,lunch=$qlunch,dinner=$qdinner,privacy=$qprivacy,concess1=$qconcess1,concess2=$qconcess2,fee={$this->Fee} where {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot update entrant record, error was $ecode", "Database error");
		}
	}
	
	public function del($t)
	{
		if  (!mysql_query("delete from {$t->etable()} where {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot delete tournament record, error was $ecode", "Database error");
		}
	}
 
	public function frompost()  {
		$this->Rank = new rank($_POST['rank']);
 		$this->Club = trim($_POST['club']);
 		$this->Country = trim($_POST['country']);
 		$this->Email = trim($_POST['email']);
		if (!preg_match('/@/', $this->Email))
			$this->Email = "";						//  Cater for "as before" which we sort out in create_or_update in player
 		$this->Nonbga = isset($_POST['nonbga']);
 		$this->Lunch = isset($_POST['lunch']);
 		$this->Dinner = isset($_POST['dinner']);
 		$this->Privacy = isset($_POST['privacy']);
 		$this->Concess1 = $this->Concess2 = false;
 		if (isset($_POST['concess']))  {
 			switch  ($_POST['concess'])  {
 			case  'C1':
 				$this->Concess1 = true;
 				break;
 			case  'C2':
 				$this->Concess2 = true;
 				break;
 			}
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
 	public function display_email() {
 		return  htmlspecialchars($this->Email);
 	}
 	public function display_fee() {
 		return sprintf("%.2f", $this->Fee);
 	}
 	public function display_edate() {
 		return $this->Edate->display();
 	}
 	
 	// Calculate basic fee for tournament taking into account early bird / late entry
 	
 	public function basic_fee($t) {
 		$totfee = $t->Fee;
 		//  See if late fee applies
 		if  ($t->Latefee > 0)  {
			$cdate = new Tdate($t->Sdate);
			$cdate->incdays(-$t->Latedays);
			if ($cdate->is_past($this->Edate))
				$totfee += $t->Latefee;
		}
		if  ($t->Ebird > 0  &&  $this->Edate->is_past($t->Ebdate))
			$totfee -= $t->Ebird;
		return  $totfee;
 	}
 	
 	public function total_fee($t) {
 		$totfee = $this->basic_fee($t);
 		if ($this->Lunch)
 			$totfee += $t->Lunch;
 		if ($this->Nonbga)
 			$totfee += $t->Nonbga;
 		if ($this->Concess1)
 			$totfee -= $t->Concess1;
 		elseif ($this->Concess2)
 			$totfee -= $t->Concess2;
 		return $totfee;
 	}
 }
 
 function get_entrants($t, $order = "rank desc,last,first")
 {
	$result = array();
	$ret = mysql_query("select first,last from {$t->etable()} order by $order");
	if  ($ret)  {
   		while ($row = mysql_fetch_array($ret)) {
   			$p = new Entrant($row[0], $row[1]);
   			$p->fetchdets($t);
     		$result.array_push($result, $p);
     	}
	}
   	return $result;
 }

//  Create and delete entrant tables.

function create_entrants($tabname)
{
	return mysql_query("create table $tabname (first tinytext,last tinytext,club tinytext," .
					"country tinytext,email tinytext,rank int default 0," .
					"nonbga tinyint(1) not null default 0,lunch tinyint(1) not null default 0," .
					"dinner tinyint(1) not null default 0,privacy tinyint(1) not null default 0," .
					"concess1 tinyint(1) not null default 0,concess2 tinyint(1) not null default 0," .
					"edate date,fee decimal(10,2) unsigned default 0.00)");     
}

function delete_entrants($tabname)
{
	return mysql_query("drop table $tabname");
}
