<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif de référence: Google           //
//                                                              //
//                   Blaise 28-01-2018   V0.1                   //
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
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');
require_once('RRDTool.php');

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


///////////////////////////////////////////////////////////////////
// Paramètres du navigateur cible pour la simulation             //
///////////////////////////////////////////////////////////////////

// Execution du navigateur sur le serveur local, disponible au port ci-dessous parce que le Java y est lancé 
//$host = 'http://localhost:4444/wd/hub';
//$host = 'http://sm00739.saintmaur.local:4444/wd/hub';
$host = 'http://test01-x.saintmaur.local:4444/wd/hub';

// Choix du navigateur
$options = new ChromeOptions();
$options->addArguments(array("--start-maximized"));
$capabilities = DesiredCapabilities::chrome();
$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

// Instanciation de la classe permettant le stockage des données en base circulaire
$filename = pathinfo(__FILE__)['filename'];
$RRD = new RRDTool($filename);

// Lancement du navigateur sur le client cible, timeout de 5 secondes
// Stockage heure de début
$driver = RemoteWebDriver::create($host, $capabilities, 10000);
$timeStart = round(microtime(true) * 1000);


///////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////

// Ouverture de la page d'accueil de l'application
$driver->get('https://www.google.fr/');
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeHome = $timeCurrent - $timeStart;
$timeLast = $timeCurrent;

// Suppression des éventuels cookies résiduels
//$driver->manage()->deleteAllCookies();


// On attend l'affichage du bloc de login
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('gb_70')));
$element->click();

// Saisie du login et du mot de passe puis validation
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('identifierId')));
$element->sendKeys('licences@mairie-saint-maur.com');
$driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();

$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('password')));
$element->clear();
$element->sendKeys('M7FohTSh');
$driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeLogin = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;


// Recherche simple sur le mot Test
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('lst-ib')));
$element->clear();
$element->sendKeys("test\n");
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeActions = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// Déconnexion
// Retour sur la home page pour éviter le problème du bouton logout caché au delà de la fenêtre
$driver->get('https://www.google.fr/');
$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector('span.gb_ab.gbii')))->click();
$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('gb_71')))->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeLogout = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// Afficher le titre de la page courante
echo "Le titre de la dernière page est: " . $driver->getTitle() . "\n";

// Afficher l'URL de la page actuelle
echo "L'URL finale est: " . $driver->getCurrentURL() . "\n";

// Sortie
fin(0, "Google OK");
