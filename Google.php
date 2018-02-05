<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Google           //
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

class Google extends scenario {

   public function gohome() {
      $driver = $this->driver;

      parent::goHome();

      // Ouverture de la page d'accueil de l'application
      $driver->get('https://www.google.fr/');
      // On attend l'affichage de la page
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('gb_70')));
   }


   public function Login() {
      $driver = $this->driver;


      parent::Login();

      // On attend l'affichage du bloc de login
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('gb_70')));
      $driver->findElement(WebDriverBy::id('gb_70'))->click();

      // Saisie du login et du mot de passe puis validation
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('identifierId')));
      $element->sendKeys('licences@mairie-saint-maur.com');
      $driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();

      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('password')));
      $element->clear();
      $element->sendKeys('M7FohTSh');
      $driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();
      
      // On attend l'affichage de la page une fois identifié
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('btnK')));
   }
   
   public function Action() {
      $driver = $this->driver;

      parent::Action();

      // Recherche simple sur le mot Test
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('lst-ib')));
      $element->clear();
      $element->sendKeys("test\n");

      // Retour à la home pour éviter le problème de largeur de page en de bouton invisible
      $driver->get('https://www.google.fr/');
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('btnK')));
   }

   public function Logout() {
      $driver = $this->driver;

      parent::Logout();

      // Déconnexion
      $driver->findElement(WebDriverBy::cssSelector('span.gb_ab.gbii'))->click();
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('gb_71')));
      $driver->findElement(WebDriverBy::id('gb_71'))->click();
   }

}
?>
