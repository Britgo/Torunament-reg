<?php
class Person {
	public $First;
	public $Last;

	public function __construct($f = "", $l = "") {
		if (strlen($f) != 0)  {
			if (strlen($l) != 0) {
				$this->First = $f;
				$this->Last = $l;
			}
			elseif (preg_match("/^(.*)\s+(\S+)$/", $f, $matches))  {
				$this->First = $matches[1];
				$this->Last = $matches[2];
			}
			elseif (preg_match("/^\S+$/", $f))  {
				$this->First = $f;
				$this->Last = "_";
			}
			else
				throw new Tcerror("Cannot parse name", "Person error");
		}
		else  {
			$this->First = $this->Last = $f;
		}
	}
	
	public function queryof($prefix = "") {
		$qf = mysql_real_escape_string($this->First);
		$ql = mysql_real_escape_string($this->Last);
		return "{$prefix}first='$qf' and {$prefix}last='$ql'";
	}
	
	public function qfirst() {
		return mysql_real_escape_string($this->First);
	}
	
	public function qlast() {
		return mysql_real_escape_string($this->Last);
	}
	
	public function urlof() {
	   $f = urlencode($this->First);
      $l = urlencode($this->Last);
      return "f=$f&l=$l";
   }

	public function fromget() {
   	$this->First = $_GET["f"];
      $this->Last = $_GET["l"];
 	}

	public function display_name() {
		if  ($this->Last == '_')
			return  htmlspecialchars($this->First);
 		return  htmlspecialchars($this->First . ' ' . $this->Last);
 	}
 	
 	public function is_same($pl) {
		return $this->First == $pl->First && $this->Last == $pl->Last;
	}
}
?>
