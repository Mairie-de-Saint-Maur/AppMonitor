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

class Institutionnel extends scenario {

   public function gohome() {
      $driver = $this->driver;
      parent::goHome();

      // Ouverture de la page d'accueil de l'application
      $driver->get('https://www.saint-maur.com/');

      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Se connecter')));

      $this->takeSnapshot();
      return 0;
   }


   public function Login() {
      $driver = $this->driver;
      parent::Login();
      
      $driver->findElement(WebDriverBy::linkText('Se connecter'))->click();
      // On attend l'affichage du bloc de login
      $element = $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user')));

      // Saisie du login et du mot de passe puis validation
      $driver->findElement(WebDriverBy::id('user'))->sendKeys('blaise.thauvin@mairie-saint-maur.com');
      $driver->findElement(WebDriverBy::id('motdepasse'))->sendKeys('OAN5NFrXf0l6GafxQSZd');
      $driver->findElement(WebDriverBy::id('submit_login'))->click();

      $this->takeSnapshot();
      return 0;
   }
   
   public function Action() {
      $driver = $this->driver;
      parent::Action();

      // On attend l'affichage effectif de la première page puis clic sur menu "mes démarches"
      //$driver->findElement(WebDriverBy::xpath("(//button[@type='button'])[5]"))->click();
      //$driver.findElement(WebDriverBy::css_selector("#mes-demarches > ul > li > a"))->click();

      // clic sur bouton "mon compte
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Mon compte')))->click();

      $this->takeSnapshot();
      return 0;
   }

   public function Logout() {
      $driver = $this->driver;
      parent::Logout();

      // Déconnexion
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Se déconnecter')))->click();

      $this->takeSnapshot();
      return 0;
   }

}
?>
