<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif Agora pour Selenium 2          //
//                                                              //
//                   Blaise 26-01-2018   V0.2                   //
//                                                              //
//////////////////////////////////////////////////////////////////

//namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

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
$host = 'http://sm00739.saintmaur.local:4444/wd/hub';
$host = 'http://test01-x.saintmaur.local:4444/wd/hub';

// Choix du navigateur
$capabilities = DesiredCapabilities::chrome();

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
$driver->get('http://10.51.0.8/agora/pck_security.home');
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeHome = $timeCurrent - $timeStart;
$timeLast = $timeCurrent;

// Suppression des éventuels cookies résiduels
$driver->manage()->deleteAllCookies();


// On cherche les 3 boutons colorés des 3 domaines d'Agora
// puis on clique aveuglément sur le deuxième 
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('dock-item'))); 
$elements = $driver->findElements(WebDriverBy::className('dock-item'));
$nbElements = count($elements);
if ($nbElements <> 3) 
   fin("On attendait 3 disques clicables, on en a obtenu $nbElements.\n");
else
   // $element[0] = Agora Baby
   // $element[1] = Agora Péri
   // $element[2] = Agora Scolaire
   $elements[0]->click();

// On attend l'affichage du bloc de login
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('p_login')));

// Saisie du login et du mot de passe puis validation
$driver->findElement(WebDriverBy::name('p_login'))->sendKeys('Tdsi');
$driver->findElement(WebDriverBy::name('p_pass'))->sendKeys('DSI94100');
$link = $driver->findElement(WebDriverBy::id('logIn'));
$link->click();

// On attend l'affichage effectif de la première page
$driver->wait()->until(WebDriverExpectedCondition::titleContains('Agor@Baby'));
//$driver->wait()->until(WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::id('title', 'login')));
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeLogin = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// Un cookie a peut-être été postionné, on l'affiche
//$cookies = $driver->manage()->getCookies();
//print_r($cookies);


// Clic sur les items de menu
$link = $driver->findElement(WebDriverBy::linkText('GESTION DE LA POPULATION'));
$link->click();

$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('FAMILLES')));
$link = $driver->findElement(WebDriverBy::linkText('FAMILLES'));
$link->click();

$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Rechercher')));
$link = $driver->findElement(WebDriverBy::linkText('Rechercher'));
$link->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeActions = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('DÉCONNEXION')));
$link = $driver->findElement(WebDriverBy::linkText('DÉCONNEXION'));
$link->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeLogout = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// Afficher le titre de la page courante
echo "Le titre de la dernière page est: " . $driver->getTitle() . "\n";

// Afficher l'URL de la page actuelle
echo "L'URL finale est: " . $driver->getCurrentURL() . "\n";

// Sortie
fin(0, "Agora OK");
