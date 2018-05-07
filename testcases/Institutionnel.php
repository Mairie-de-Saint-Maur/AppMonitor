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

class Institutionnel extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

   public function Home() {
      // Ouverture de la page d'accueil de l'application
      $this->driver->get('https://www.saint-maur.com/');

      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Se connecter')));
   }


   public function Login() {
      $this->driver->findElement(WebDriverBy::linkText('Se connecter'))->click();
      // On attend l'affichage du bloc de login
      $element = $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user')));

      // Saisie du login et du mot de passe puis validation
      $this->driver->findElement(WebDriverBy::id('user'))->sendKeys('blaise.thauvin@mairie-saint-maur.com');
      $this->driver->findElement(WebDriverBy::id('motdepasse'))->sendKeys('OAN5NFrXf0l6GafxQSZd');
      $this->driver->findElement(WebDriverBy::id('submit_login'))->click();
   }
   
   public function Action() {
      // On attend l'affichage effectif de la première page puis clic sur menu "mes démarches"
      //$this->driver->findElement(WebDriverBy::xpath("(//button[@type='button'])[5]"))->click();
      //$driver.findElement(WebDriverBy::css_selector("#mes-demarches > ul > li > a"))->click();

      // clic sur bouton "mon compte
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Mon compte')))->click();
   }

   public function Logout() {
      // Déconnexion
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Se déconnecter')))->click();
   }

}
?>
