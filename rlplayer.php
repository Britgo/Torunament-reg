<?php

class RLplayer extends Person {
 	
 	public $Rank;
 	public $Club;
 	public $Email;
  	public $Nonbga;
 	public $Concess;
 	
	public function __construct($f = "", $l = "", $club, $em, $rk, $clublist)  {
		parent::__construct($f, $l);
		$this->Rank = new rank($rk);
		if (isset($clublist[$club]))
 			$this->Club = $clublist[$club];
 		else
 			$this->Club = "No Club";
 		$this->Email = $em;
 		$this->Nonbga = false;
 		$this->Concess = false;
 	}
}
 
function get_clubs()
{
	$result = array();
	$ret = mysql_query("select code,name from club");
	if  ($ret)  {
   		while ($row = mysql_fetch_array($ret)) {
   			$result[$row[0]] = $row[1];
     	}
	}
	return $result;
}

function get_rlplayers($cl)
{
	$result = array();
	$ret = mysql_query("select first,last,club,email,rank from player where suppress=0 and since > date_sub(current_date,interval 6 month) order by last,first,rank desc");
	if  ($ret)  {
		while ($row = mysql_fetch_array($ret))  {
			$p = new RLplayer($row[0], $row[1], $row[2], $row[3], $row[4], $cl);
			array_push($result, $p);
		}
	}
	return $result;
}

function open_rldb()
{
	//  CHANGE THESE AS REQUIRED!!!!

	$hostname = "localhost";
	$username = "rluser";
	$password = "Get Ratings";
	$dbname = "ratinglist";
	
	if  (!mysql_connect($hostname, $username, $password)  ||  !mysql_select_db($dbname))  {
		$ecode = mysql_error();
		throw  new  Tcerror("Cannot open RL database, error was $ecode", "Database error");
	}
}
?>
