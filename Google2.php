<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Google           //
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

class Google2 extends scenario {

   public function gohome() {
      $driver = $this->driver;
      global $step; 
      $step = 'Home';

      // Ouverture de la page d'accueil de l'application
      $driver->get('https://www.google.fr/');
      // On attend l'affichage de la page
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('gb_70')));

      takeSnapshot();
      return 0;
   }


   public function Login() {
      global $step;
      $driver = $this->driver;
      $step = 'Login';

      // On attend l'affichage du bloc de login
      $element = $driver->findElement(WebDriverBy::id('gb_70'));
      $element->click();

      // Saisie du login et du mot de passe puis validation
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('identifierId')));
      $element->sendKeys('licences@mairie-saint-maur.com');
      $driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();

      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('password')));
      $element->clear();
      $element->sendKeys('M7FohTSh');
      $driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();
      
      // On attend l'affichage de la page une fois identifié
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('btnK')));

      takeSnapshot();
      return 0;
   }
   
   public function Action() {
      global $step;
      $driver = $this->driver;
      $step = 'Action';

      // Recherche simple sur le mot Test
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('lst-ib')));
      $element->clear();
      $element->sendKeys("test\n");

      // Retour à la home pour éviter le problème de largeur de page en de bouton invisible
      $driver->get('https://www.google.fr/');
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('btnK')));

      takeSnapshot();
      return 0;
   }

   public function Logout() {
      global $step;
      $driver = $this->driver;
      $step = 'Logout';

      // Déconnexion
      $driver->findElement(WebDriverBy::cssSelector('span.gb_ab.gbii'))->click();
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('gb_71')));
      $driver->findElement(WebDriverBy::id('gb_71'))->click();

      takeSnapshot();
      return 0;
   }

}
?>
