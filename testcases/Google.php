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

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

   public function Home() {
      // Ouverture de la page d'accueil de l'application
      $this->driver->get('https://www.google.fr/');
      // On attend l'affichage de la page
      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('gb_70')));
   }


   public function Login() {
      // On attend l'affichage du bloc de login
      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('gb_70')));
      $this->driver->findElement(WebDriverBy::id('gb_70'))->click();

      // Saisie du login et du mot de passe puis validation
      $element = $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('identifierId')));
      $element->sendKeys('licences@mairie-saint-maur.com');
      $this->driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();

      $element = $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::name('password')));
      $element->clear();
      $element->sendKeys('#####');
      $this->driver->findElement(WebDriverBy::cssSelector('span.RveJvd.snByac'))->click();
      
      // On attend l'affichage de la page une fois identifié
      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('btnK')));
   }
   
   public function Action() {
      // Recherche simple sur le mot Test
      $element = $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('lst-ib')));
      $element->clear();
      $element->sendKeys("test\n");

      // Retour à la home pour éviter le problème de largeur de page en de bouton invisible
      $this->driver->get('https://www.google.fr/');
      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('btnK')));
   }

   public function Logout() {
      // Déconnexion
      $this->driver->findElement(WebDriverBy::cssSelector('span.gb_ab.gbii'))->click();
      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('gb_71')));
      $this->driver->findElement(WebDriverBy::id('gb_71'))->click();
   }

}
?>
