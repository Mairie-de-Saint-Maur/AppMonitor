<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif du site institutionnel         //
//                                                              //
//                   Blaise 26-01-2018   V0.1                   //
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

///////////////////////////////////////////////////////////////////
//    Gestion des exceptions                                     //
///////////////////////////////////////////////////////////////////
function exception_handler($exception) {
   // Prenons une copie d'écran à tout hasard....
   global $driver;
   if (isset($driver)) {
      $screenshot = "screenshot-". time() . ".png";
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
      global $driver, $RRD;
   
      echo "$message\n";
   
      // Détruit la classe RRDTool (provoque la sauvegarde des données)
      // Si le script a échoué: screenshot
      if ($RRD->timeLogout == 'U') {
         $screenshot = "screenshot-". time() . ".png";
         $driver->takeScreenshot($screenshot);
         $exit_code = 1;
      }
      unset($RRD);
   
      // Ferme le navigateur
      $driver->quit();
      exit($exit_code);
   
   }

///////////////////////////////////////////////////////////////////
// Classe de gestion des logs circulaires                        //
///////////////////////////////////////////////////////////////////
class RRDTool {

   private $rrdTool = '/opt/rrdtool-1.7.0/bin/rrdtool';
   private $rrdUpdate = '/opt/rrdtool-1.7.0/bin/rrdupdate';
   private $rrdFile = 'default.rrd';

   public $timeHome    = 'U';
   public $timeLogin   = 'U';
   public $timeActions = 'U';
   public $timeLogout  = 'U';

   // Création du fichier RRD si nécessaire lors de l'instanciation
   function __construct($file) {
      $path_parts = pathinfo($file);
      $this->rrdFile = $path_parts['dirname'] . "/" . $path_parts['filename'] . ".rrd";
      if (!file_exists($this->rrdFile)) {
         $parameters = "--step 60 --no-overwrite DS:home:GAUGE:600:0:60000 DS:login:GAUGE:600:0:60000 \
           DS:actions:GAUGE:600:0:60000 \
           DS:logout:GAUGE:600:0:60000 \
           RRA:AVERAGE:0.5:1:2880 \
           RRA:AVERAGE:0.5:5:2304 \
           RRA:AVERAGE:0.5:30:700 \
           RRA:AVERAGE:0.5:120:775 \
           RRA:AVERAGE:0.5:1440:3700 \
           RRA:MIN:0.5:1:2880 \
           RRA:MIN:0.5:5:2304 \
           RRA:MIN:0.5:30:700 \
           RRA:MIN:0.5:120:775 \
           RRA:MIN:0.5:1440:3700 \
           RRA:MAX:0.5:1:2880 \
           RRA:MAX:0.5:5:2304 \
           RRA:MAX:0.5:30:700 \
           RRA:MAX:0.5:120:775 \
           RRA:MAX:0.5:1440:3700 \
           RRA:LAST:0.5:1:2880 \
           RRA:LAST:0.5:5:2304 \
           RRA:LAST:0.5:30:700 \
           RRA:LAST:0.5:120:775 \
           RRA:LAST:0.5:1440:3700";
         exec("$this->rrdTool create $this->rrdFile $parameters", $output, $errno);
         if ( $errno <> 0 ) print_r($output);
         return $errno;
      }
   }

   // La destruction de la classe entraine l'enregistrement du log avec les valeurs par défaut
   // Cela permet de conserver la trace des plantages dans les données d'exécution
   function __destruct() {
      $this->update();
   }

   // Update du fichier rrd
   public function update() {
      $timeHome = $this->timeHome;
      $timeLogin = $this->timeLogin;
      $timeActions = $this->timeActions;
      $timeLogout = $this->timeLogout;

      echo "Home:    $timeHome ms\n";
      echo "Login:   $timeLogin ms\n";
      echo "Actions: $timeActions ms\n";
      echo "Logout:  $timeLogout ms\n";
      echo "Total: " . ($timeHome + $timeLogin + $timeActions + $timeLogout) . "ms\n";
      exec("$this->rrdUpdate $this->rrdFile -t home:login:actions:logout N:$timeHome:$timeLogin:$timeActions:$timeLogout", $output, $errno
);
      if ( $errno <> 0 ) print_r($output);
      return $errno;
   }

}


///////////////////////////////////////////////////////////////////
// Paramètres du navigateur cible pour la simulation             //
///////////////////////////////////////////////////////////////////

// Execution du navigateur sur le serveur local, disponible au port ci-dessous parce que le Java y est lancé 
//$host = 'http://localhost:4444/wd/hub';
//$host = 'http://sm00739.saintmaur.local:4444/wd/hub';
$host = 'http://test01-x.saintmaur.local:4444/wd/hub';

// Choix du navigateur
$capabilities = DesiredCapabilities::firefox();

// Instanciation de la classe permettant le stockage des données en base circulaire
$RRD = new RRDTool(__FILE__);

// Lancement du navigateur sur le client cible, timeout de 5 secondes
// Stockage heure de début
$driver = RemoteWebDriver::create($host, $capabilities, 10000);
$timeStart = round(microtime(true) * 1000);


///////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////

// Ouverture de la page d'accueil de l'application
$driver->get('https://www.saint-maur.com/');
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeHome = $timeCurrent - $timeStart;
$timeLast = $timeCurrent;

// Suppression des éventuels cookies résiduels
$driver->manage()->deleteAllCookies();


$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Se connecter'))); 
$link = $driver->findElement(WebDriverBy::linkText('Se connecter'));
$link->click();

// On attend l'affichage du bloc de login
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user')));

// Saisie du login et du mot de passe puis validation
$driver->findElement(WebDriverBy::id('user'))->sendKeys('blaise.thauvin@mairie-saint-maur.com');
//$driver->findElement(WebDriverBy::id('motdepasse')->clear();
$driver->findElement(WebDriverBy::id('motdepasse'))->sendKeys('OAN5NFrXf0l6GafxQSZd');
$link = $driver->findElement(WebDriverBy::id('submit_login'));
$link->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeLogin = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// On attend l'affichage effectif de la première page puis clic sur menu "mes démarches"
//$driver->findElement(WebDriverBy::xpath("(//button[@type='button'])[5]"))->click();
//$driver.findElement(WebDriverBy::css_selector("#mes-demarches > ul > li > a"))->click();

// clic sur bouton "mon compte
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Mon compte')));
$element->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeActions = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// Déconnexion
$element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Se déconnecter')));
$element->click();
$timeCurrent = round(microtime(true) * 1000);
$RRD->timeLogout = $timeCurrent - $timeLast;
$timeLast = $timeCurrent;

// Afficher le titre de la page courante
echo "Le titre de la dernière page est: " . $driver->getTitle() . "\n";

// Afficher l'URL de la page actuelle
echo "L'URL finale est: " . $driver->getCurrentURL() . "\n";

// Sortie
fin(0, "Institutionnel OK");
