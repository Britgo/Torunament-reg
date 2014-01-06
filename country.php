<?php

class Country {
	public  $Name;
		
	public function __construct($n = "")
	{
		$this->Name = strlen($n) == 0? "UK": $n;
	}
	
	public function queryof()
	{
		$qname = mysql_real_escape_string($this->Name);
		return "name='$qname'";
	}
	
	public function create()
	{
		$qname = mysql_real_escape_string($this->Name);
		$ret = mysql_query("select name from country where {$this->queryof()}");
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
	
	public function display_name()
	{
		return htmlspecialchars($this->Name);
	}
}

function list_countries()
{
	$result = array();
	$ret = mysql_query("select name from country order by name");
	if  ($ret)
		while  ($row = mysql_fetch_array($ret))  {
			array_push($result, new Country($row[0]));
		}
	return $result;
}
?>