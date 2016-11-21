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


if (!isset($_POST['r1']) || !isset($_POST['r2']) || !isset($_POST['asp']))  {
	$mess = "No sums";
	include 'php/wrongentry.php';
	exit(0);
}

$r1 = $_POST['r1'];
$r2 = $_POST['r2'];
$asp = strtolower($_POST['asp']);
$ans = array('zero','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty');
if  ($ans[$r1+$r2] != $asp) {
	$fh = fopen($_SERVER["DOCUMENT_ROOT"] . '/Badsums', 'a');
	fwrite($fh, "Bad login on " . date("/M/Y H:i:s\n"));
	foreach ($_SERVER as $n => $v) {
		fwrite($fh, "SERVER[$n] = $v\n");
	}
	foreach ($_POST as $n -> $v) {
		fwrite($fh, "POST[$n] = $v\n");
	}
	fwrite($fh, "\n\n");
	fclose($fh);
	$Title = 'Wrong sum';
	include 'php/head.php';
	print <<<EOT
<body>
<h1>Incorrect spam challenge</h1>
<p>Sorry but the answer to your sum was wrong, please try again - remember it should be as a word, like "eleven".</p>
</body>
</html>

EOT;
	flush();
	sleep(30);
	exit(111);
}
?>
