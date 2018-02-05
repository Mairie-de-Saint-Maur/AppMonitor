#!/usr/bin/php
<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif pour Selenium 2                //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////


require_once('vendor/autoload.php');

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;


require_once('vendor/phpmailer/phpmailer/class.phpmailer.php');
require_once('vendor/phpmailer/phpmailer/class.smtp.php');
require_once('RRDTool.php');
require_once('Scenario.php');

///////////////////////////////////////////////////////////////////
//    Gestion des exceptions                                     //
///////////////////////////////////////////////////////////////////
function exception_handler($exception) {
   global $scenario, $mail, $driver, $error, $step, $parameter;


   // Prenons une copie d'écran à tout hasard....
   if (is_object($driver)) takeSnapshot();

   fwrite(STDERR, "Catch\n". $exception->getMessage() . "\nCatch\n");
   if (is_object($mail)) {
      $mail->Body = $mail->Body . "<br>" . $exception->getMessage() . "<br>" ;
      $mail->Subject = $mail->Subject . " étape $step";
      $mail->send();
   }
   $error += 1;
   return $error;
}

$error = 0;
set_exception_handler('exception_handler');

///////////////////////////////////////////////////////////////////
//  Sortie propre                                                //
///////////////////////////////////////////////////////////////////

   function fin($exit_code=0, $message='fin de simulation') {
      global $driver, $RRD, $mail;

      addBody("$message<br>");
      $mail->Subject = "Sortie normale code $exit_code, message $message";
      echo "$message\n";

      // Si le script a échoué 
      if ($error > 0 || $exit_code > 0) {
         $exit_code = $error;
         $mail->send();
      }

      // Ferme le navigateur
      if (is_object($driver)) $driver->quit();
      exit($exit_code);

   }

///////////////////////////////////////////////////////////////////
// Calcul du temps d'execution de chaque étape                   //
///////////////////////////////////////////////////////////////////
function logTime() {
   global $timeLast;

   $timeCurrent = round(microtime(true) * 1000);
   $elapsed = $timeCurrent - $timeLast;
   $timeLast = $timeCurrent;
   return $elapsed;
}


///////////////////////////////////////////////////////////////////
// Gestion des mails                                             //
///////////////////////////////////////////////////////////////////

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
//Server settings
$mail->SMTPDebug = 0;                                 // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.saintmaur.local';                 // Specify main and backup SMTP servers
$mail->SMTPAuth = false;                               // Enable SMTP authentication

//Recipients
$mail->setFrom('Supervision_Applicative@mairie-saint-maur.com', 'Supervision Applicative');
$mail->addAddress('blaise.thauvin@mairie-saint-maur.com', 'Blaise Thauvin');     // Add a recipient
//$mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre'); 
$mail->addReplyTo('blaise.thauvin@mairie-saint-maur.com', 'Blaise Thauvin');

//Content
//$mail->Charset('UTF-8');
$mail->setLanguage('fr', '/opt/AppMonitor/vendor/phpmailer/phpmailer/language/');
$mail->isHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Echec scenario';
$mail->Body    = '';


function addBody($text) {
   global $mail;
   $mail->Body = $mail->Body . $text;
}

///////////////////////////////////////////////////////////////////
// Prend un snapshot de l'état courant du test en indiquant l'heure et l'étape
///////////////////////////////////////////////////////////////////
function takeSnapshot() {
   global $mail, $driver, $error, $step, $parameter;

   if (is_object($driver)) {
      $screenshot = "screenshot-$parameter-$step-". date("Y-m-d_H-i-s") . ".png";
      try {
         $driver->takeScreenshot($screenshot);
      }
      catch(Exception $e) {
         fwrite(STDERR, "Impossible de prendre une copie d'écran\n");
         $mail->Body = $mail->Body . "<br>Impossible de prendre une copie d'écran.<br>" . $e->getMessage() . "<br>" ;
         $error += 1;
         return $error;
      }
      $mail->addAttachment($screenshot);
   }
   return 0;
}

///////////////////////////////////////////////////////////////////
// Paramètres du navigateur cible pour la simulation             //
///////////////////////////////////////////////////////////////////

// Execution du navigateur sur le serveur local, disponible au port ci-dessous parce que le Java y est lancé 
//$host = 'http://localhost:4444/wd/hub';
$host = 'http://sm00739.saintmaur.local:4444/wd/hub';
$host = 'http://test01-x.saintmaur.local:4444/wd/hub';

// Choix du navigateur
$options = new ChromeOptions();
$options->addArguments(array("--start-maximized"));
$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);


// Lancement du navigateur sur le client cible, timeout de 5 secondes
// Stockage heure de début
try {
   $driver = RemoteWebDriver::create($host, $capabilities, 10000);
} 
catch(Exception $e) {
   global $error;
   fwrite(STDERR, "$e->getMessage()");
   addBody($e->getMessage());
   $mail->Subject = "Impossible de lancer le navigateur";
   $error +=1;
}   

//////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////

// Boucle sur les arguments passés en ligne de commande
foreach ($argv as $key => $parameter) {
   if ($key == 0) continue; 

   addBody("<br>$parameter<br>");
   echo "\nScenario $parameter\n-----------------\n"; 
   $mail->Subject = "ECHEC Scenario $parameter";

   // Instanciation de la classe de scénario
   require_once("$parameter.php");
   $scenario = new $parameter($driver, $mail);

   // Instanciation de la classe permettant le stockage des données en base circulaire
   $RRD = new RRDTool($parameter);

   if ($error == 0) {
      $step = 'Home';
      logTime();
      $scenario->goHome();
      $RRD->timeHome = logTime();
      takeSnapshot();
   }

   if ($error == 0) {
      $step = 'Login';
      logTime();
      $scenario->Login();
      $RRD->timeLogin = logTime();
      takeSnapshot();
   }

   if ($error == 0) {   
      $step = 'Actions';   
      logTime();
      $scenario->Action();
      $RRD->timeActions = logTime();
      takeSnapshot();
   }
   if ($error == 0) {
      $step = 'Logout';
      logTime();
      $scenario->Logout();
      $RRD->timeLogout = logTime();
      takeSnapshot();
   }

   // Suppression des éventuels cookies résiduels
   if (is_object($driver)) {
      try {
         $driver->manage()->deleteAllCookies();
      }
      catch(Exception $e) {
         addBody("Aucun cookie à supprimer<br>");
         echo "Aucun cookie à supprimer\n";
      }
   }
   // Enregistrement des données par destruction de la classe RRD
   addBody("Home:    $RRD->timeHome ms<br>");
   addBody( "Login:   $RRD->timeLogin ms<br>");
   addBody("Actions: $RRD->timeActions ms<br>");
   addBody("Logout:  $RRD->timeLogout ms<br>");
   addBody("Total:   " . ($RRD->timeHome + $RRD->timeLogin + $RRD->timeActions + $RRD->timeLogout) . " ms<br>");
   if ( $RRD->timeLogout == 'U' ) $error+=1;
   unset($RRD);

   // Afficher le titre de la page courante
   if (is_object($driver)) addBody("Le titre de la dernière page est: " . $driver->getTitle() . "<br>");

   // Afficher l'URL de la page actuelle
   if (is_object($driver)) addBody("L'URL finale est: " . $driver->getCurrentURL() . "<br>");

   echo "Niveau d'erreur = $error\n";

   // Destruction de la classe de scénario
   unset($scenario);
  
   addBody("Scenario $parameter OK<br>------------------<br>");
   if ($error > 0 ) {
      try {
         $mail->send();
         echo "Message has been sent\n";
         $mail->Body = '';
         $mail->clearAttachments();
      } 
      catch (Exception $e) {
         fwrite(STDERR, "Message could not be sent. Mailer Error:\n". $mail->ErrorInfo . "\n");
      }
   }
   array_map('unlink', glob("screenshot-$parameter-*.png"));
   $error = 0;
}

// Fermeture du navigateur et sortie
fin(0, "\nTests Selenium OK\n");

?>
