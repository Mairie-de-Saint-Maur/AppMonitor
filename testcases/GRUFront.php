<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif GU Front              //
//                                                              //
//                   Camus 23-04-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class GRUFront extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://capdemat-front-prp.multimediabs.com/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".avia-menu.av-main-nav-wrap")));
	}

	public function Login() {
		//parent::Login();

		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://capdemat-front-prp.multimediabs.com/connexion');
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user_login')))->clear();
		$this->driver->findElement(WebDriverBy::id('user_login'))->sendKeys("alt.r2-9ok7tvlt@yopmail.com");
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user_pass')))->clear();
		$this->driver->findElement(WebDriverBy::id('user_pass'))->sendKeys("CLgru94100");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//Vérification du chargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#widget-compte')));
		
	}
   
	public function Action() {
		//Clic sur le lien de changement d'adresse et vérification
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText("Modifier mon adresse mail")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#email')));
		//Clic sur le lien de changement de mot de passe et vérification
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText("Modifier mon mot de passe")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#pwd')));
	}

	public function Logout() {
		//Clic sur le bouton déconnexion et vérification
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText("Déconnexion")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#layer_slider_1')));
		
	}
}
?>
