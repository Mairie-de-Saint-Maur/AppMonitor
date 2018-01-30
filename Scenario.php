<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//            classe générique de tests applicatifs             //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////

//namespace Facebook\WebDriver;


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Scenario {

   function __construct($driver) {
      $this->driver = $driver;
      set_exception_handler('exception_handler'); 
   }

   // Gestion des exceptions
   function exception_handler($exception) {
      global $step;
      $driver = $this->driver;
      $name = pathinfo(__FILE__)['filename'];

      // Prenons une copie d'écran à tout hasard....
      if (is_object($driver)) {
         $screenshot = "screenshot-$name-$this->step-". date("Y-m-d_H:i:s") . ".png";
         try {
            $driver->takeScreenshot($screenshot);
         }
         catch(Exception $e) {
            echo "Impossible de prendre une copie d'écran";
            touch($screenshot);
         }
     }
     fin( 1, "Exception attrapée : ". $exception->getMessage() . "!\n");
   }


   // Prend un snapshot de l'état courant du test en indiquant l'heure et l'étape
   function takeSnapshot() {
      global $step;
      $driver = $this->driver;
      $name = pathinfo(__FILE__)['filename'];

      if (is_object($driver)) {
         $screenshot = "screenshot-$name-$step-". date("Y-m-d_H:i:s") . ".png";
         try {
            $driver->takeScreenshot($screenshot);
         }
         catch(Exception $e) {
            echo "Impossible de prendre une copie d'écran";
            touch($screenshot);
            return 1;
         }
      return 0;
      }
   }

   public function gohome() {
      $driver = $this->driver;
      global $step; 
      $step = 'Home';

      sleep(1);
      $this->takeSnapshot();
      return 0;
   }


   public function Login() {
      $driver = $this->driver;
      global $step;
      $step = 'Login';

      sleep(1);
      $this->takeSnapshot();
      return 0;
   }
   
   public function Action() {
      $driver = $this->driver;
      global $step;
      $step = 'Action';

      sleep(1);
      $this->takeSnapshot();
      return 0;
   }

   public function Logout() {
      $driver = $this->driver;
      global $step;
      $step = 'Logout';

      sleep(1);
      $this->takeSnapshot();
      return 0;
   }

}
?>
