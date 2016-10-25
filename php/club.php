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
		return "name='$qname'";
	}

	public function urlof()
	{
		$qc = urlencode($this->Name);
		return "clubname=$qc";
	}	
	
	public function create()
	{
		$qname = mysql_real_escape_string($this->Name);
		$qcnt = mysql_real_escape_string($this->Country);
		$qq = $this->queryof();
		$ret = mysql_query("select name from clubs where $qq");
		if  (!$ret)  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot access clubs record, error was $ecode", "Database error");
		}
		if  (mysql_num_rows($ret) > 0)
			return;
		if  (!mysql_query("insert into clubs (name,country) values ('$qname','$qcnt')"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot create clubs record, error was $ecode", "Database error");
		}
	}
	
	public function update()
	{
		$qcnt = mysql_real_escape_string($this->Country);
		if  (!mysql_query("update clubs set country='$qcnt' where {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot update clubs record, error was $ecode", "Database error");
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
}

function optcreate_club($club, $cnt) {
	$clb = new Club($club, $cnt);
	$clb->create();
}

function list_clubs()
{
	$result = array();
	$ret = mysql_query("select name,country from clubs order by name,country");
	if  ($ret)
		while  ($row = mysql_fetch_array($ret))  {
			array_push($result, new Club($row[0], $row[1]));
		}
	return $result;
}
?>