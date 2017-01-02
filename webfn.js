//   Copyright 2009 John Collins

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

function nonblank(s)  {
        for (var i = 0;  i < s.length;  i++)  {
                var c = s.charAt(i);
                if  (c != ' '  &&  c != '\t'  &&  c != '\n')
                        return  true;
        }
        return  false;
}

function okcode(s)  {
	return /^\w+$/.test(s);
}

function okname(s)  {
	return /^ *[A-Z][a-z]*( +(Mc|O')?[A-Z][a-z]*(-[A-Z][a-z]*)?)+$/.test(s); //'
}

function okclubcountry(s)  {
	return /^[A-Z][- a-zA-Z]*[a-z]$/.test(s);
}

function checkchallenge(s) {
	return /^[a-zA-Z]+$/.test(s);
}

function lostpw() {
	var uidv = document.getElementById('user_id');
	var l = uidv.value;
	if (!nonblank(l)) {
		 alert("No userid given");
       return;
   }
   window.open("rempwbyuid.php?uid=" + l, "Password Reminder", "width=450,height=200,resizeable=yes,scrollbars=yes");
}

// For checking prices in things

function isprice(s, descr)  {
	var  v = s.value;
	if  (!/^\d+\.\d\d$/.test(v))
		throw Error("Invalid price value for " + descr);
	return  parseFloat(s.value);
}

// For checking bits of dates

function getsel(el, descr) {
	var si = el.selectedIndex;
	if  (si < 0)
		throw Error("No " + descr + " selected");
	return el.options[si].value;
}
function datecheck(fmyr, fmmon, fmdy, descr)
{
   	var ds = getsel(fmdy, "Day for " + descr);
    	var ms = getsel(fmmon, "Month for " + descr);
    	var ys = getsel(fmyr, "Year for " + descr);
    	var tdat = new Date(ys, ms-1, ds, 12, 0, 0);
   	var now = new Date();
   	if  (tdat < now)
     	throw Error("Time for " + descr + " has to be in future");
	  	if (tdat.getMonth() != ms-1)
  			throw Error("Invalid date for " + descr);
		return tdat;
}
