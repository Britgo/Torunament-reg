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


class Club {
	public  $Name;
	public  $Country;
	
	public function __construct($n = "", $c = "")
	{
		$this->Name = strlen($n) == 0? "No Club": $n;
		$this->Country = strlen($c) == 0? "UK": $c;
	}
	
	public function queryof()
	{
		$qname = mysql_real_escape_string($this->Name);
		$qcount = mysql_real_escape_string($this->Country);
		return "name='$qname' AND country='$qcount'";
	}
	
	public function qname()
	{
		return  mysql_real_escape_string($this->Name);
	}
	
	public function qcountry() {
		return  mysql_real_escape_string($this->Country);
	}

	public function urlof()  {
		$qc = urlencode($this->Name);
		$qcnt = urlencode($this->Country);
		return "clubname=$qc&countname=$qcnt";
	}
	
	public function fromget($prefix = "") {
      $this->Name = $_GET["${prefix}clubname"];
      $this->Country = $_GET["${prefix}countname"];
      if (strlen($this->Name) == 0 || strlen($this->Country) == 0)
      	throw new Tcerror("Null get club name field"); 
   }

	public function save_hidden($prefix = "") {
   	$qname = htmlspecialchars($this->Name);
   	$qcnt = htmlspecialchars($this->Country);
      return "<input type=\"hidden\" name=\"${prefix}clubname\" value=\"$qname\"><input type=\"hidden\" name=\"${prefix}countname\" value=\"$qcnt\">";
   }
	
	public function from_post($prefix = "")  {
		$this->Name = $_POST["${prefix}clubname"];
		$this->Country = $_POST["${prefix}countname"];
		if  (strlen($this->Name) == 0 || strlen($this->Country) == 0)
			throw new Tcerror("Null post club name field");
	}
	
	public function is_same($other)
	{
		return  strcasecmp($this->Name, $other->Name) == 0 && strcasecmp($this->Country, $other->Country) == 0;
	}

	public function check_clashes() {
		$qq = $this->queryof();
		$ret = mysql_query("SELECT COUNT(*) FROM clubs WHERE $qq");
		if  (!$ret)  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot access clubs record, error was $ecode", "Database error");
		}
		$row = mysql_fetch_array($ret);
		return  $row[0] > 0;
	}
	
	public function create()
	{
		if  ($this->check_clashes())
			return;		
		$qname = mysql_real_escape_string($this->Name);
		$qcnt = mysql_real_escape_string($this->Country);
		if  (!mysql_query("INSERT INTO clubs (name,country) VALUES ('$qname','$qcnt')"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot create clubs record, error was $ecode", "Database error");
		}
	}
	
	public function update($newclub)  {
		$qq = $this->queryof();
		$qname = mysql_real_escape_string($newclub->Name);
		$qcnt = mysql_real_escape_string($newclub->Country);
		if  (!mysql_query("UPDATE clubs SET name='$qname',country='$qcnt' WHERE $qq"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot uodate clubs record, error was $ecode", "Database error");
		}
	}
	
	public function del()
	{
		$ret = mysql_query("DELETE FROM clubs WHERE {$this->queryof()}");
		if  (!$ret)  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot delete clubs record, error was $ecode", "Database error");
		}
	}
	
	public function display_name()
	{
		return  htmlspecialchars($this->Name);
	}
	
	public function display_country()
	{
		return  htmlspecialchars($this->Country);
	}
	
	public static function optcreate_club($club, $cnt) {
		$clb = new Club($club, $cnt);
		$clb->create();
		return  $clb;
	}

	public static function list_clubs() {
		$result = array();
		$ret = mysql_query("SELECT name,country FROM clubs ORDER BY name,country");
		if  ($ret)
			while  ($row = mysql_fetch_array($ret))  {
				array_push($result, new Club($row[0], $row[1]));
			}
		return $result;
	}
}
?>