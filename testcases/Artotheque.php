<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Artothèque       //
//                                                              //
//                   Blaise 16-03-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Artotheque extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['gohome','Login','Action','Logout'];
   }

   public function gohome() {
      // Ouverture de la page d'accueil de l'application
      $this->driver->get('http://saint-maur.ideesculture.fr/gestion/');

      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('username')));
   }


   public function Login() {
      // Saisie du login et du mot de passe puis validation
      $this->driver->findElement(WebDriverBy::name('username'))->clear();
      $this->driver->findElement(WebDriverBy::name('username'))->sendKeys('test');
      $this->driver->findElement(WebDriverBy::name('password'))->clear();
      $this->driver->findElement(WebDriverBy::name('password'))->sendKeys('test2018!');
      $this->driver->findElement(WebDriverBy::linkText('Identifiant'))->click();
   }
   
   public function Action() {
      // clic sur bouton "Mes Préférences"
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Préférences')))->click();
   }

   public function Logout() {
      // Déconnexion
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Déconnexion')))->click();
   }

}
?>
