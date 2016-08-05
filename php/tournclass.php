<?php

// Copyright John Collins 2014
// Licensed under the GPL, v3

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

class Tournament {
	public $Tcode;
	public $Name;
	public $Tclass;
 	public $Format;
 	public $Overview;
 	public $Address;
 	public $Postcode;
 	public $Sdate;
 	public $Ndays;
 	public $Nrounds;
 	public $Provisional;
 	public $Open;
 	public $Fee;
 	public $Concess1;				// Discount for concession type 1
 	public $Concess2;				// Discount for concession type 2
 	public $Concess1name;			// Description for concession type 1
 	public $Concess2name;			// Description for concession type 2
 	public $Nonbga;
 	public $Lunch;
 	public $Dinner;					// Dinner or such function, blank if none
 	public $Ebird;				// Early bird discount (0.00 if none)
 	public $Ebdate;
 	public $Latefee;
 	public $Latedays;
 	public $Champion;				// Current champion (person object)
 	public $Contact;				// Contact (person object)
 	public $Email;				// Contact email
 	public $Website;				// Tournament website
 	
	public function __construct($tc = "") {
 		$this->Tcode = $tc;
 		$this->Name = "";
 		$this->Tclass = "A";
 		$this->Format = "";
 		$this->Overview = "";
 		$this->Address = "";
 		$this->Postcode = "";
		$this->Sdate = new Tdate();
 		$this->Ndays = 1;
 		$this->Nrounds = 1;
 		$this->Provisional = false;
 		$this->Open = true;
 		$this->Fee = 5.0;
 		$this->Concess1 = 3.0;
 		$this->Concess2 = 0.0;
 		$this->Concess1name = "Concession";
 		$this->Concess2name = "";
 		$this->Nonbga = 3.0;
 		$this->Lunch = 0.0;
 		$this->Dinner = "";
 		$this->Ebird = 0.0;
 		$this->Ebdate = new Tdate();
 		$this->Ebdate->incdays(-30);
 		$this->Latefee = 5.0;
 		$this->Latedays = 0;
 		$this->Champion = new Person();
 		$this->Contact = new Person();
 		$this->Email = "";
 		$this->Website = "";
 	}
 	
 	public function clonefrom($t)  {
 		$dat = getdate();
 		$year = $dat['year'];
 		$this->Name = preg_replace('/20\d\d/', $year, $t->Name);
 		$this->Tclass = $t->Tclass;
 		$this->Format = $t->Format;
 		$this->Overview = $t->Overview;
 		$this->Address = $t->Address;
 		$this->Postcode = $t->Postcode;
		$this->Ndays = $t->Ndays;
 		$this->Nrounds = $t->Nrounds;
 		$this->Fee = $t->Fee;
 		$this->Concess1 = $t->Concess1;
 		$this->Concess2 = $t->Concess2;
 		$this->Concess1name = $t->Concess1name;
 		$this->Concess2name = $t->Concess2name;
 		$this->Nonbga = $t->Nonbga;
 		$this->Lunch = $t->Lunch;
 		$this->Dinner = $t->Dinner;
 		$this->Ebird = $t->Ebird;
 		$this->Latefee = $t->Latefee;
 		$this->Latedays = $t->Latedays;
 		$this->Champion = $t->Champion;
 		$this->Contact = $t->Contact;
 		$this->Email = $t->Email;
 		$this->Website = $t->Website;
 	}
 		
 	public function isnew() {
 		return strlen($this->Tcode)  ==  0;
 	}
 	
 	public function queryof()  {
 		$qt = mysql_real_escape_string($this->Tcode);
 		return "tcode='$qt'";
 	}
 	
 	public function urlof()  {
 		$qt = urlencode($this->Tcode);
 		return  "?tcode=$qt";
 	}
 	
 	public function etable() {
 		return $this->Tcode . "_entries";
 	}
 	
 	public function fetchdets()  {
 		$ret = mysql_query("select " .
 						"tname," .
 						"tclass," .
 						"format," .
 						"overview," .
 						"address," .
 						"postcode," .
 						"sdate," .
 						"ndays," .
 						"rounds," .
 						"provisional," .
 						"open," .
 						"fee," .
 						"lunch," .
 						"dinner," .
 						"concess1," .
 						"concess2," .
 						"concess1name," .
 						"concess2name," .
 						"nonbga," .
 						"ebird," .
 						"ebdate," .
 						"latefee," .
 						"latedays," .
 						"champfirst," .
 						"champlast," .
 						"contactfirst," .
 						"contactlast," .
 						"email," .
 						"website from tdetails where {$this->queryof()}");
 		if  (!$ret)
 			throw new Tcerror("Cannot read tournament details", "Fetch tournament details error");
 		if  (mysql_num_rows($ret) == 0)
 			throw new Tcerror("No rows found tcode={$this->Tcode}". "Read fail");
 		$row = mysql_fetch_assoc($ret);
 		$this->Name = $row['tname'];
 		$this->Tclass = $row['tclass'];
 		$this->Format = $row['format'];
 		$this->Overview = $row['overview'];
 		$this->Address = $row['address'];
 		$this->Postcode = $row['postcode'];
 		$this->Sdate->enctime($row['sdate']);
		$this->Ndays = $row['ndays'];
 		$this->Nrounds = $row['rounds'];
 		$this->Provisional = $row["provisional"];
 		$this->Open = $row["open"];
		$this->Fee = $row['fee'];
		$this->Lunch = $row['lunch'];
		$this->Dinner = $row['dinner'];
 		$this->Concess1 = $row['concess1'];
 		$this->Concess2 = $row['concess2'];
 		$this->Concess1name = $row['concess1name'];
 		$this->Concess2name = $row['concess2name'];
 		$this->Nonbga = $row['nonbga'];
 		$this->Ebird = $row['ebird'];
 		$this->Ebdate->enctime($row['ebdate']);
 		$this->Latefee = $row['latefee'];
 		$this->Latedays = $row['latedays'];
 		$this->Champion = new Person($row['champfirst'], $row['champlast']);
 		$this->Contact = new Person($row['contactfirst'], $row['contactlast']);
 		$this->Email = $row['email'];
 		$this->Website = $row['website'];
 	}
 		
	public function create()	
 	{
		$qcode = mysql_real_escape_string(rtrim($this->Tcode));
		$qname = mysql_real_escape_string($this->Name);
		$qclass = mysql_real_escape_string($this->Tclass);
		$qformat = mysql_real_escape_string($this->Format);
		$qover = mysql_real_escape_string($this->Overview);
		$qaddr = mysql_real_escape_string($this->Address);
		$qpc = mysql_real_escape_string($this->Postcode);
		$qdinner = mysql_real_escape_string($this->Dinner);
		$qc1 = mysql_real_escape_string($this->Concess1name);
		$qc2 = mysql_real_escape_string($this->Concess2name);
		$qem = mysql_real_escape_string($this->Email);
		$qws = mysql_real_escape_string($this->Website);
		$qprov = $this->Provisional? 1: 0;
		$qopen = $this->Open? 1: 0;
		$qdat = $this->Sdate->queryof();
		$qebdat = $this->Ebdate->queryof();
		if  (!mysql_query("insert into tdetails " .
					    "(tcode,tname,tclass," .
 						"format,overview,address,postcode," .
 						"sdate,ndays,rounds,provisional,open,dinner," .
 						"fee,lunch,concess1,concess2,concess1name,concess2name," .
 						"nonbga,ebird,ebdate,latefee,latedays," .
 						"champfirst,champlast,contactfirst,contactlast,email,website" .
 						") values (" .
						"'$qcode','$qname','$qclass','$qformat','$qover','$qaddr','$qpc'," .
						"'$qdat',{$this->Ndays},{$this->Nrounds},$qprov,$qopen,'$qdinner'," .
						"{$this->Fee},{$this->Lunch},{$this->Concess1},{$this->Concess2},'$qc1','$qc2'," .
						"{$this->Nonbga},{$this->Ebird},'$qebdat',{$this->Latefee},{$this->Latedays}," .
						"'{$this->Champion->qfirst()}','{$this->Champion->qlast()}','{$this->Contact->qfirst()}','{$this->Contact->qlast()}','$qem','$qws')"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot create tournament record, error was $ecode", "Database error");
		}
		if  (!create_entrants($this->etable()))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot create entrants table, error was $ecode", "Database error");
		}
	}
	
	public function update()
	{
		$qname = mysql_real_escape_string($this->Name);
		$qclass = mysql_real_escape_string($this->Tclass);
		$qformat = mysql_real_escape_string($this->Format);
		$qover = mysql_real_escape_string($this->Overview);
		$qaddr = mysql_real_escape_string($this->Address);
		$qpc = mysql_real_escape_string($this->Postcode);
		$qdinner = mysql_real_escape_string($this->Dinner);
		$qc1 = mysql_real_escape_string($this->Concess1name);
		$qc2 = mysql_real_escape_string($this->Concess2name);
		$qem = mysql_real_escape_string($this->Email);
		$qws = mysql_real_escape_string($this->Website);
		$qprov = $this->Provisional? 1: 0;
		$qopen = $this->Open? 1: 0;
		$qdat = $this->Sdate->queryof();
		$qebdat = $this->Ebdate->queryof();

		if  (!mysql_query("update tdetails set " .
						"tname='$qname',tclass='$qclass'," .
						"format='$qformat',overview='$qover',address='$qaddr',postcode='$qpc'," .
						"sdate='$qdat',ndays={$this->Ndays},rounds={$this->Nrounds},provisional=$qprov,open=$qopen,dinner='$qdinner'," .
						"fee={$this->Fee},lunch={$this->Lunch},concess1={$this->Concess1},concess2={$this->Concess2},concess1name='$qc1',concess2name='$qc2'," .
						"nonbga={$this->Nonbga},ebird={$this->Ebird},ebdate='$qebdat'," .
						"latefee={$this->Latefee},latedays={$this->Latedays}," .
						"champfirst='{$this->Champion->qfirst()}',champlast='{$this->Champion->qlast()}'," .
						"contactfirst='{$this->Contact->qfirst()}',contactlast='{$this->Contact->qlast()}'," .
						"email='$qem',website='$qws'" .
						" where {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot update tournament record, error was $ecode", "Database error");
		}
	}
	
	public function del()
	{
		if  (!mysql_query("delete from tdetails where {$this->queryof()}"))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot delete tournament record, error was $ecode", "Database error");
		}
		if  (!delete_entrants($this->etable()))  {
			$ecode = mysql_error();
			throw  new  Tcerror("Cannot delete entrance table, error was $ecode", "Database error");
		}
	}
 
	public function frompost()  {
 		$this->Name = trim($_POST['tname']);
 		$this->Tclass = $_POST['tclass'];
 		$this->Format = trim($_POST['format']);
 		$this->Overview = trim($_POST['overview']);
 		$this->Address = trim($_POST['address']);
 		$this->Postcode = trim($_POST['postcode']);
 		$this->Sdate->frompost();
		$this->Ndays = $_POST['ndays'];
 		$this->Nrounds = $_POST['rounds'];
 		$this->Provisional = isset($_POST["provisional"]);
 		$this->Open = isset($_POST["open"]);
		$this->Fee = $_POST['fee'];
		$this->Lunch = $_POST['lunch'];
		$this->Dinner = "";
		if (isset($_POST['dinner']))
			$this->Dinner = trim($_POST['dinner']);
 		$this->Concess1 = $_POST['concess1'];
 		$this->Concess2 = $_POST['concess2'];
 		$this->Concess1name = trim($_POST['concess1name']);
 		$this->Concess2name = trim($_POST['concess2name']);
 		$this->Nonbga = $_POST['nonbga'];
 		$this->Ebird = $_POST['ebird'];
 		$this->Ebdate->frompost('eb');
 		$this->Latefee = $_POST['latefee'];
 		$this->Latedays = $_POST['latedays'];
 		$this->Champion = new Person($_POST['champ']);
 		$this->Contact = new Person($_POST['contact']);
 		$this->Email = trim($_POST['email']);
 		$this->Website = trim($_POST['website']);
	}
 		
 	public function set_hidden()  {
 		  print "<input type=\"hidden\" name=\"tcode\" value=\"{$this->Tcode}\" />\n";
 	}
 	
 	public function display_code() {
 		return htmlspecialchars($this->Tcode);
 	}
 	public function display_name() {
 		return  htmlspecialchars($this->Name);
 	}
 	public function display_class() {
 		return $this->Tclass;
 	}
 	public function display_format() {
 		return  htmlspecialchars($this->Format);
 	}
 	public function display_over() {
 		return  htmlspecialchars($this->Overview);
 	}
 	public function display_addr() {
 		return  htmlspecialchars($this->Address);
 	}
 	public function html_format() {
 		return preg_replace("/\n+/", "</p>\n<p>", $this->display_format());
 	}
 	public function html_over() {
 		return preg_replace("/\n+/", "</p>\n<p>", $this->display_over());
 	}
 	public function html_addr() {
 		return preg_replace("/\n+/", "</p>\n<p>", $this->display_addr());
 	}
 	public function display_pc() {
 		return  htmlspecialchars($this->Postcode);
 	}
 	public function display_fee() {
 		return sprintf("%.2f", $this->Fee);
 	}
 	public function display_lunch() {
 		return sprintf("%.2f", $this->Lunch);
 	}
 	public function display_dinner() {
 		return  htmlspecialchars($this->Dinner);
 	}
 	public function display_nonbga() {
 		return sprintf("%.2f", $this->Nonbga);
 	}
 	public function display_concess1() {
 		return sprintf("%.2f", $this->Concess1);
 	}
 	public function display_concess2() {
 		return sprintf("%.2f", $this->Concess2);
 	}
 	public function display_concess1name() {
 		return htmlspecialchars($this->Concess1name);
 	}
 	public function display_concess2name() {
 		return htmlspecialchars($this->Concess2name);
 	}
 	public function display_latefee() {
 		return sprintf("%.2f", $this->Latefee);
 	}
 	public function display_ebird() {
 		return sprintf("%.2f", $this->Ebird);
 	}
 	public function display_email() {
 		return htmlspecialchars($this->Email);
 	}
 	public function display_ws() {
 		return htmlspecialchars($this->Website);
 	}
 	public function display_dates() {
 		$sd = $this->Sdate->display();
 		if  ($this->Ndays <= 1)
 			return  $sd;
 		$ld = new Tdate($this->Sdate);
		$ld->incdays($this->Ndays - 1);
		return $sd . " - " . $ld->display();
	}
 	
 	// Calculate basic fee from today's date
 	
 	public function calc_basic_fee() {
 		$totfee = $this->Fee;
 		//  See if late fee applies
 		if  ($this->Latefee > 0)  {
			$cdate = new Tdate($this->Sdate);
			$cdate->incdays(-$this->Latedays);
			if ($cdate->is_past())
				$totfee += $this->Latefee;
		}
		if  ($this->Ebird > 0  &&  $this->Ebdate->is_future())
			$totfee -= $this->Ebird;
		return  $totfee;
 	}
 	
 	public function display_basic_fee() {
 		return sprintf("%.2f", $this->calc_basic_fee());
 	}
 	
 	public function is_over() {
 		$t = new Tdate($this->Sdate);
 		//$t->incdays(-1);
 		return $t->is_past();
 	}
 	
 	public function count_entries() {
 		$result = 0;
 		$ret = mysql_query("select count(*) from {$this->etable()}");
 		if  ($ret && ($row = mysql_fetch_array($ret)))
 			$result = $row[0];
 		return  $result;
 	}
 }
 
 function get_tcodes($order = "tcode", $openonly = false, $futureonly = false)
 {
	$result = array();
	$constr = "";
	if  ($openonly && $futureonly)
		$constr = " where open!=0 and date_add(sdate,interval ndays day)>current_date()";
	elseif  ($openonly)
		$constr = " where open!=0";
	elseif  ($futureonly)
		$constr = " where date_add(sdate,interval ndays day)>current_date()";
	$ret = mysql_query("select tcode from tdetails$constr order by $order");
	if  ($ret)  {
   		while ($row = mysql_fetch_array($ret)) {
     			array_push($result, $row[0]);
     		}
	}
	else {
		$code = mysql_error();
		throw new Tcerror("Error was $code", "List fail");
	}
	
	return $result;
 }
 
 function tourn_select()
 {
	$ret = mysql_query("select tcode,tname from tdetails order by tname");
	if  (!$ret  ||  mysql_num_rows($ret) == 0)
		throw new Tcerror("No tournaments to select from", "No tournaments");
	print <<<EOT
<select name="tselect">

EOT;
	while  ($row = mysql_fetch_array($ret))  {
		$tcode = urlencode($row[0]);
		$tname = htmlspecialchars($row[1]);
		print <<<EOT
<option value="$tcode">$tname</option>

EOT;
	}
	print "</select>\n"; 	
}
?>
