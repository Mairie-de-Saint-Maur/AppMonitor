<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//            classe générique de tests applicatifs             //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////



use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Scenario {

   function __construct($driver, $mail) {
      $this->driver = $driver;
      $this->mail = $mail;
      set_exception_handler('exception_handler'); 
   }

   // Gestion des exceptions
   function exception_handler($exception) {
      global $step;
      $driver = $this->driver;
      $mail = $this->mail;

      // Prenons une copie d'écran à tout hasard....
      if (is_object($driver)) {
         $screenshot = "screenshot-" . get_class($this) . "-$this->step-". date("Y-m-d_H:i:s") . ".png";
         try {
            $driver->takeScreenshot($screenshot);
         }
         catch(Exception $e) {
            echo "Impossible de prendre une copie d'écran";
            $mail->Body = $mail->Body . "Impossible de prendre une copie d'écran à  l'étape $this->step<br>";
            touch($screenshot);
         }
     }
     $mail->Body = $mail->Body . "<br>" . $exception->getMessage() . "<br>" ;
     return 1;
   }


   // Prend un snapshot de l'état courant du test en indiquant l'heure et l'étape
   function takeSnapshot() {
      global $step;
      $driver = $this->driver;

      if (is_object($driver)) {
         $screenshot = "screenshot-" . get_class($this) ."-$step-". date("Y-m-d_H:i:s") . ".png";
         try {
            $driver->takeScreenshot($screenshot);
         }
         catch(Exception $e) {
            echo "Impossible de prendre une copie d'écran";
            touch($screenshot);
            return 1;
         }
         $this->mail->addAttachment($screenshot);
      }
      return 0;
   }

   public function gohome() {
      global $step; 
      $step = 'Home';

      return 0;
   }


   public function Login() {
      global $step;
      $step = 'Login';

      return 0;
   }
   
   public function Action() {
      global $step;
      $step = 'Action';

      return 0;
   }

   public function Logout() {
      global $step;
      $step = 'Logout';

      return 0;
   }

}
?>
