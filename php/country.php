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


class Country {
	public  $Name;
		
	public function __construct($n = "")
	{
		$this->Name = strlen($n) == 0? "UK": $n;
	}
	
	public function is_same($other)
	{
		return  $this->Name == $other->Name;		// Don't ignore case!
   }

   public function queryof()
   {
      $qname = mysql_real_escape_string($this->Name);
      return "name='$qname'";
   }

   public function qname() {
      return  mysql_real_escape_string($this->Name);
   }

   public function urlof()
   {
      $qc = urlencode($this->Name);
      return "countryname=$qc";
   }

   public function fromget($prefix = "") {
      $this->Name = $_GET["${prefix}countryname"];
      if (strlen($this->Name) == 0)
         throw new Tcerror("Null get country name field");
   }

   public function from_post($prefix = "")  {
      $this->Name = $_POST["${prefix}countryname"];
      if  (strlen($this->Name) == 0)
         throw new Tcerror("Null post country name field");
   }

   public function save_hidden($prefix = "") {
      $qname = htmlspecialchars($this->Name);
      return "<input type=\"hidden\" name=\"${prefix}countryname\" value=\"$qname\">";
   }

   public function create()
   {
      $qname = mysql_real_escape_string($this->Name);
      $ret = mysql_query("select name from country where name='$qname'");
      if  (!$ret)  {
         $ecode = mysql_error();
         throw  new  Tcerror("Cannot access country record, error was $ecode", "Database error");
      }
      if  (mysql_num_rows($ret) != 0)
         return;
      if  (!mysql_query("insert into country (name) values ('$qname')"))  {
         $ecode = mysql_error();
         throw  new  Tcerror("Cannot create country record, error was $ecode", "Database error");
      }
   }

   public function update($newinst)
   {
      $ret = mysql_query("UPDATE country SET name='{$newinst->qname()}' WHERE {$this->queryof()}");
      if  (!$ret)  {
         $ecode = mysql_error();
         throw  new  Tcerror("Cannot update country record, error was $ecode", "Database error");
      }
   }

   public function del()
   {
      $ret = mysql_query("DELETE FROM country WHERE {$this->queryof()}");
      if  (!$ret)  {
         $ecode = mysql_error();
         throw  new  Tcerror("Cannot delete country record, error was $ecode", "Database error");
      }
   }

   public function check_clashes() {
      $qq = $this->queryof();
      $ret = mysql_query("SELECT COUNT(*) FROM country WHERE $qq");
      if  (!$ret)  {
         $ecode = mysql_error();
         throw new Tcerror("Cannot access country record, error was $ecode", "Database error");
      }
      $row = mysql_fetch_array($ret);
      return  $row[0] > 0;
   }

   public function display_name()
   {
      return htmlspecialchars($this->Name);
   }

   public static function optcreate_country($c)  {
      $cnt = new Country($c);
      $cnt->create();
      return $cnt;
   }

   public static function list_countries()  {
      $result = array();
      $ret = mysql_query("select name from country order by name");
      if  ($ret)
         while  ($row = mysql_fetch_array($ret))  {
            array_push($result, new Country($row[0]));
         }
      return $result;
   }

   public static function countryopt($existing = "", $selfn = "") {
      $countries = self::list_countries();
      $onc = "";
      if (strlen($selfn) != 0)
         $onc = " onchange=\"$selfn();\"";
      print "<select name=\"countrysel\"$onc>\n";
      print "<option value='none'>None Selected</option>\n";
      foreach ($countries as $country) {
         $qname = htmlspecialchars($country->Name);
         if ($country->Name == $existing)
            print "<option value=\"$qname\" selected>$qname</option>\n";
         else
            print "<option value=\"$qname\">$qname</option>\n";
      }
      print "</select>\n";
   }
}
?>