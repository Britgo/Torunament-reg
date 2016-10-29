<?php

// Class for exceptions during tournament creation handling

class Tcerror extends Exception {

   public $Header;

   public function __construct($msg, $hdr = "Tournament System Error") {
      parent::__construct($msg);
      $this->Header = $hdr;
   }
}
?>
