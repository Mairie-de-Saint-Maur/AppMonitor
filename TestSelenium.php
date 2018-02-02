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
   global $parameter, $mail, $error;
   $error += 1;
   addBody("<br>$exception->getMessage()<br>");
   $mail->Subject = $mail->Subject . "erreur, arret script $parameter";
   fin( 1, "Exception attrapée : ". $exception->getMessage() . "!\n");
}

$error = 0;
set_exception_handler('exception_handler');

///////////////////////////////////////////////////////////////////
//  Sortie propre                                                //
///////////////////////////////////////////////////////////////////

   function fin($exit_code=0, $message='fin de simulation') {
      global $driver, $RRD, $filename, $mail;

      addBody("$message<br>");
      echo "$message\n";

      // Si le script a échoué et que $driver est bien un objet: screenshot
      if ($RRD->timeLogout == 'U') {
         $exit_code = 1;
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
$mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre'); 
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
   echo "Impossible de lancer le navigateur\n";
   $mail->Subject = "Impossible de lancer le navigateur";
   $error +=1;
   echo $e->getMessage();
   addBody($e->getMessage());
}   
//////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////

// Instanciation de la classe de scénario
foreach ($argv as $key => $parameter) {
   if ($key == 0) continue; 

   addBody("<br>$parameter<br>");
   $mail->Subject = "ECHEC Scenario $parameter";

   // Instanciation de la classe de scénario
   require_once("$parameter.php");
   $scenario = new $parameter($driver, $mail);

   // Instanciation de la classe permettant le stockage des données en base circulaire
   $RRD = new RRDTool($parameter);
   $timeLast = round(microtime(true) * 1000);

   if ($error == 0) {
      $error += $scenario->goHome();
      $RRD->timeHome = logTime();
   }

   if ($error == 0) {
      $error += $scenario->Login();
      $RRD->timeLogin = logTime();
   }

   if ($error == 0) {      
      $error += $scenario->Action();
      $RRD->timeActions = logTime();
   }
   if ($error == 0) {
      $error += $scenario->Logout();
      $RRD->timeLogout = logTime();
   }

   // Suppression des éventuels cookies résiduels
   try {
      $driver->manage()->deleteAllCookies();
   }
   catch(Exception $e) {
      addBody("Aucun cookie à supprimer<br>");
   }
   // Enregistrement des données par destruction de la classe RRD
   addBody("Home:    $RRD->timeHome ms<br>");
   addBody( "Login:   $RRD->timeLogin ms<br>");
   addBody("Actions: $RRD->timeActions ms<br>");
   addBody("Logout:  $RRD->timeLogout ms<br>");
   addBody("Total:   " . ($RRD->timeHome + $RRD->timeLogin + $RRD->timeActions + $RRD->timeLogout) . " ms<br>");
   unset($RRD);

   // Afficher le titre de la page courante
   addBody("Le titre de la dernière page est: " . $driver->getTitle() . "<br>");

   // Afficher l'URL de la page actuelle
   addBody("L'URL finale est: " . $driver->getCurrentURL() . "<br>");

   // Destruction de la classe de scénario
   unset($scenario);
  
   addBody("Scenario $parameter OK<br>------------------<br>");
   if ($error >0) {
      try {
         $mail->send();
         echo "Message has been sent\n";
         $mail->Body = '';
         $mail->clearAttachments();
      } 
      catch (Exception $e) {
         echo "Message could not be sent. Mailer Error:\n". $mail->ErrorInfo . "\n";
      }
   }
   array_map('unlink', glob("screenshot-$parameter-*.png"));
}

// Fermeture du navigateur et sortie
fin(0, "\nTests Selenium OK\n");

?>
