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

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
#      $mail->addAddress('blaise.thauvin@mairie-saint-maur.com', 'Blaise Thauvin');
   }

   public function gohome() {
      $driver = $this->driver;
      parent::goHome();

      // Ouverture de la page d'accueil de l'application
      $driver->get('http://saint-maur.ideesculture.fr/gestion/');

      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('username')));
   }


   public function Login() {
      $driver = $this->driver;
      parent::Login();
      
      // Saisie du login et du mot de passe puis validation
      $driver->findElement(WebDriverBy::name('username'))->clear();
      $driver->findElement(WebDriverBy::name('username'))->sendKeys('test');
      $driver->findElement(WebDriverBy::name('password'))->clear();
      $driver->findElement(WebDriverBy::name('password'))->sendKeys('test2018!');
      $driver->findElement(WebDriverBy::linkText('Identifiant'))->click();
   }
   
   public function Action() {
      $driver = $this->driver;
      parent::Action();

      // clic sur bouton "Mes Préférences"
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Préférences')))->click();
   }

   public function Logout() {
      $driver = $this->driver;
      parent::Logout();

      // Déconnexion
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Déconnexion')))->click();
   }

}
?>
