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

if (isset($_SESSION['user_id']))  {
	$uid = $_SESSION['user_id'];
	if (strlen($uid) != 0) {
		include 'php/tcerror.php';
		include 'php/opendb.php';
		opendb();
		$quid = mysql_real_escape_string($uid);
		if (!mysql_query("delete from player where user='$quid'"))  {
			$mess = mysql_error();
			include 'php/wrongentry.php';
			exit(0);
		}
	} 
}
ini_set("session.gc_maxlifetime", "18000");
$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "/phpsessions";
if (is_dir($phpsessiondir))
	session_save_path($phpsessiondir);
session_set_cookie_params(604800);
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_priv']);
?>
<html>
<head>
<title>Deleted userid</title>
</head>
<body onload="onl();">
<script language="javascript">
function onl() {
	document.location = "index.php";
}
</script>
</body>
</html>
