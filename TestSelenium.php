<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Classes de base pour tests Selenium                      //
//                                                              //
//            Blaise 20-01-2018   V0.1                          //
//                                                              //
//////////////////////////////////////////////////////////////////

//namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

require_once('vendor/autoload.php');

class TestSelenium {

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
      global $driver;
   
      echo "$message\n";
   
      // Détruit la classe RRDTool (provoque la sauvegarde des données)
      unset($RRD);
   
      // Ferme le navigateur
      $driver->quit();
      exit($exit_code);
   
   }
   
   ///////////////////////////////////////////////////////////////////
   // Paramètres du navigateur cible pour la simulation             //
   ///////////////////////////////////////////////////////////////////
   function __construct($testCase='') {

      if ($testCase = '') fin(1, "Un nom de scénario doit être founi en paramètre");

      // Execution du navigateur sur le serveur local, disponible au port ci-dessous parce que le Java y est lancé 
      //$host = 'http://localhost:4444/wd/hub';
      $host = 'http://sm00739.saintmaur.local:4444/wd/hub';

      // Choix du navigateur
      $capabilities = DesiredCapabilities::firefox();

      // Instanciation de la classe permettant le stockage des données en base circulaire
      $this->RRD = new RRDTool($testCase);

      // Lancement du navigateur sur le client cible, timeout de 5 secondes
      // Stockage heure de début
      $this->driver = RemoteWebDriver::create($host, $capabilities, 10000);
      $this->timeStart = round(microtime(true) * 1000);
   }

   ///////////////////////////////////////////////////////////////////
   // Fonction d'exécution du scénario de test (à surcharger)       //
   ///////////////////////////////////////////////////////////////////
   public doScenario() {
   
   }

   public Summary() {

      // Afficher le titre de la page courante
      echo "Le titre de la dernière page est: " . $driver->getTitle() . "\n";

      // Afficher l'URL de la page actuelle
      echo "L'URL finale est: " . $driver->getCurrentURL() . "\n";
   }
}
