<?php

// Open database and throw error if not nice.

function opendb()
{

	//  CHANGE THESE AS REQUIRED!!!!

	$hostname = "localhost";	
	$username = "tourn-reg";
	$password = "tourn-reg-0601";
	$dbname = "tournaments";
	
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
