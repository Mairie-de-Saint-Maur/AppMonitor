<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: AIRS Delib       //
//                                                              //
//                   Camus 26-03-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Impressions extends scenario {

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('https://impression.mairie-saint-maur.com/app_imprimerie/');
		
		//Le site va rediriger vers la page de login du SSO
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".navbar-brand")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();
		
		//On rentre dans la frame login:
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#form_authentification")));
		
		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::cssSelector("input#login"))->clear();
		$driver->findElement(WebDriverBy::cssSelector("input#login"))->sendKeys("thomas.devos");
		$driver->findElement(WebDriverBy::cssSelector("input#mdp"))->clear();
		$driver->findElement(WebDriverBy::cssSelector("input#mdp"))->sendKeys("UCI4yL0hOyvAMPI81m5D");
		
		$driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a.btn.btn-success")));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();
		
		//On affiche le menu d'abord
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a.btn.btn-success")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("imprBackHome")))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		//On affiche le menu d'abord
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href='index.php?ctrl=AuthentificationCtrl&action=deconnexion']")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("login")));
	}
}
?>