<?php
// Copyright John Collins 2016
// Licensed under the GPL, v3

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

$Title = htmlspecialchars($Title);
$qmess = htmlspecialchars($mess);
include 'head.php';
?>
<body>
<?php
print <<<EOT
<h1>$Title</h1>
<p>The following error occurred on this page.</p>
<p>$qmess</p>

EOT;
}
?>
<p>Please try again from the top by <a href="/index.php">clicking here</a>.</p>
</body>
</html>
