<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//            classe générique de tests applicatifs             //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////

class Scenario {
   public $step;

   function __construct($driver, $mail) {
      $this->step = 'unset';
      $this->driver = $driver;
      $this->mail = $mail;
      $this->err = 0;
   }


   public function gohome() {
      $this->step = 'Home';
      $this->err = 0;
   }


   public function Login() {
      $this->step = 'Login';
      $this->err = 0;
   }
   
   public function Action() {
      $this->step = 'Action';
      $this->err = 0;
   }

   public function Logout() {
      $this->step = 'Logout';
      $this->err = 0;
   }

}
?>
