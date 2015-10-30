<?php

// Copyright John Collins 2015
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

$r1 = $_POST['r1'];
$r2 = $_POST['r2'];
$asp = strtolower($_POST['asp']);
$ans = array('zero','one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty');
if  ($ans[$r1+$r2] != $asp) {
print <<<EOT
<h1>Bad sum</h1>
<p>Sorry but the answer to your sum was wrong, please try again - remember it should be figures, like "eleven".</p>

EOT;
	flush();
	sleep(300);
	exit(111);
}
?>
