<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif pour Selenium 2                //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////

//namespace Facebook\WebDriver;

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

require_once('vendor/autoload.php');
require_once('RRDTool.php');
require_once('Scenario.php');
require_once('Google2.php');

///////////////////////////////////////////////////////////////////
//    Gestion des exceptions                                     //
///////////////////////////////////////////////////////////////////
function exception_handler($exception) {
   // Prenons une copie d'écran à tout hasard....
   global $driver, $filename;
   if (isset($driver)) {
      $screenshot = "screenshot-$filename-". time() . ".png";
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

set_exception_handler('exception_handler');

///////////////////////////////////////////////////////////////////
//  Sortie propre                                                //
///////////////////////////////////////////////////////////////////

   function fin($exit_code=0, $message='fin de simulation') {
      global $driver, $RRD, $filename;

      echo "$message\n";


      // Si le script a échoué et que $driver est bien un objet: screenshot
      if ($RRD->timeLogout == 'U' && is_object($driver)) {
         $screenshot = "screenshot-$filename-". time() . ".png";
         $driver->takeScreenshot($screenshot);
         $exit_code = 1;
      }

      // Détruit la classe RRDTool (provoque la sauvegarde des données)
      unset($RRD);

      // Ferme le navigateur
      if (is_object($driver)) $driver->quit();
      exit($exit_code);

   }
// Gestion du temps d'execution de chaque étape
function logTime() {
   global $timeLast;

   $timeCurrent = round(microtime(true) * 1000);
   $elapsed = $timeCurrent - $timeLast;
   $timeLast = $timeCurrent;
   return $elapsed;
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
$driver = RemoteWebDriver::create($host, $capabilities, 10000);


///////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////


// Instanciation de la classe permettant le stockage des données en base circulaire
$filename = pathinfo(__FILE__)['filename'];
$RRD = new RRDTool($filename);

// Instanciation de la classe de scénario 
$scenario = new Scenario($driver);
$timeLast = round(microtime(true) * 1000);

$scenario->goHome();
$RRD->timeHome = logTime();

$scenario->Login();
$RRD->timeLogin = logTime():

$scenario->Action();
$RRD->timeActions = logTime();

$scenario->Logout();
$RRD->timeLogout = logTime();

// Suppression des éventuels cookies résiduels
$driver->manage()->deleteAllCookies();

// Enregistrement des données par destruction de la classe RRD
unset($RRD);

// Afficher le titre de la page courante
echo "Le titre de la dernière page est: " . $driver->getTitle() . "\n";

// Afficher l'URL de la page actuelle
echo "L'URL finale est: " . $driver->getCurrentURL() . "\n";

// Destruction de la classe de scénario
unset($scenario);

// Fermeture du navigateur et sortie
fin(0, "$filename OK");
