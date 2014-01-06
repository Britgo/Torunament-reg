<?php

// Open database and throw error if not nice.

function opendb()
{

	//  CHANGE THESE AS REQUIRED!!!!

	$hostname = "localhost";	
	$username = "c9442893_ts";
	$password = "Tent12";
	$dbname = "c9442893_tourneys";
	
	if  (!mysql_connect($hostname, $username, $password)  ||  !mysql_select_db($dbname))  {
		$ecode = mysql_error();
		throw  new  Tcerror("Cannot open database, error was $ecode", "Database error");
	}
}

function closedb()
{
	mysql_close();
}
?>
