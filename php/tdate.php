<?php

//  Class for handy date options

class Tdate {
   private $timestamp;

   public function __construct($t = null) {
      if ($t)
         $this->timestamp = $t->timestamp;
      else  {
         $dat = getdate();
         $this->timestamp = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
      }
   }

   public function enctime($ds) {
      if (preg_match('/(\d+).(\d+).(\d+)/', $ds, $rm)) {
         $yr = $rm[1];
         $mn = $rm[2];
         $dy = $rm[3];
         $this->timestamp = mktime(12,0,0,$mn,$dy,$yr);
      }
   }

   //  Get from form possibly with prefix

   public function frompost($prefix = "")  {
      $yr = $_POST["{$prefix}year"];
      $mn = $_POST["{$prefix}month"];
      $dy = $_POST["{$prefix}day"];
      $this->timestamp = mktime(12,0,0,$mn,$dy,$yr);
   }

   public function display() {
      return date("D j M Y", $this->timestamp);
   }

   public function disp_abbrev() {
      return date("d/m/y", $this->timestamp);
   }

   //  Produce a JavaScript date for the given Tdate suitable for calculations in cutoff days

   public function jsdate() {
      $dat = getdate($this->timestamp);
      $y = $dat["year"];
      $m = $dat["mon"]-1;
      $d = $dat["mday"];
      return "new Date($y, $m, $d, 0, 0, 0)";
   }

   public function display_month() {
      return date("F Y", $this->timestamp);
   }

   public function icsdate() {
      return date("Ymd", $this->timestamp);
   }

   public function shortdate() {
      return date("d/m/y", $this->timestamp);
   }

   public function queryof() {
      return date("Y-m-d", $this->timestamp);
   }

   public function querymon() {
      return date("m/Y", $this->timestamp);
   }

   public function unequal($d) {
      return $this->timestamp != $d->timestamp;
   }

   public function is_past($t = NULL) {
      if  ($t)
         $dat = getdate($t->timestamp);
      else
         $dat = getdate();
      $now = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
      return $now > $this->timestamp;
   }

   public function is_future() {
      $dat = getdate();
      $now = mktime(12,0,0,$dat["mon"], $dat["mday"], $dat["year"]);
      return $now < $this->timestamp;
   }

   public function monthstart() {
      $times = getdate($this->timestamp);
      return  mktime(1, 0, 0, $times["mon"], 1, $times["year"]);
   }

   public function yropt($prefix="") {
      $dat = getdate($this->timestamp);
      $yrsel = $dat["year"];
      print "<select name=\"{$prefix}year\">\n";
      for ($i = 2013;  $i <= 2030;  $i++) {
         if ($i == $yrsel)
            print "<option selected>$i</option>\n";
         else
            print "<option>$i</option>\n";
      }
      print "</select>\n";
   }

   public function monopt($prefix="")
   {
      $dat = getdate($this->timestamp);
      $monsel = $dat["mon"];
      $Mnames = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
      print "<select name=\"{$prefix}month\">\n";
      for ($i = 1;  $i <= 12; $i++) {
         if ($i == $monsel)
            print "<option value=$i selected>";
         else
            print "<option value=$i>";
         print $Mnames[$i-1];
         print "</option>\n";
      }
      print "</select>\n";
   }

   public function dayopt($prefix="")
   {
      $dat = getdate($this->timestamp);
      $daysel = $dat["mday"];
      print "<select name=\"{$prefix}day\">\n";
      for ($i = 1;  $i <= 31; $i++) {
         if ($i == $daysel)
            print "<option selected>$i</option>\n";
         else
            print "<option>$i</option>\n";
      }
      print "</select>\n";
   }

   public function dateopt($prefix="")
   {
      $this->dayopt($prefix);
      $this->monopt($prefix);
      $this->yropt($prefix);
   }

   public function haschanged($omd)
   {
      return $this->timestamp != $omd->timestamp;
   }

   public function sortby($omd)
   {
      return $this->timestamp - $omd->timestamp;
   }

   public function incdays($inc = 1) {
      $times = getdate($this->timestamp);
      $yr = $times["year"];
      $mon = $times["mon"];
      $day = $times["mday"];
      $this->timestamp = mktime(12,0,0,$mon, $day+$inc, $yr);
   }

   public function daysbetween($omd) {
      $db = 24 * 60 * 60;
      return round(($omd->timestamp - $this->timestamp) / $db);
   }
}

?>
