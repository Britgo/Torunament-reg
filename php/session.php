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

ini_set("session.gc_maxlifetime", "604800");
$phpsessiondir = $_SERVER["DOCUMENT_ROOT"] . "phpsessions";
if (is_dir($phpsessiondir))
	session_save_path($phpsessiondir);
session_set_cookie_params(604800);
session_start();

if (isset($_SESSION['user_id'])) {
	$userid = $_SESSION['user_id'];
	$username = $_SESSION['user_name'];
	$userpriv = $_SESSION['user_priv'];
	$logged_in = strlen($userid) != 0;
	$admin = false;
	$organ = false;
	if ($logged_in)  {
		switch ($userpriv) {
			case 'A':case 'SA';
				$admin = true;
				$organ = true;
				break;
			case  'O':
				$organ = true;
				break;
			}
		}
}
else {
	$userid = "";
	$username = "";
	$userpriv = "N";
	$admin = false;
	$organ = false;
	$logged_in = false;
}
?>
