<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif GRU Back              //
//                                                              //
//                   Camus 30-05-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class GRUBack extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://capdemat-gru-prp.multimediabs.com/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".form-signin-heading")));
	}

	public function Login() {
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user_name')))->clear();
		$this->driver->findElement(WebDriverBy::id('user_name'))->sendKeys("test.dsi");
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('user_password')))->clear();
		$this->driver->findElement(WebDriverBy::id('user_password'))->sendKeys("Betat4st200");
		
		$this->driver->findElement(WebDriverBy::id("login_button"))->click();

		//Vérification du chargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.logo')));
		
	}
   
	public function Action() {
		//Clic sur le lien de changement d'adresse et vérification
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText("Demandes")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#num_demande_basic')));
		//Clic sur le lien de changement de mot de passe et vérification
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText("Individus")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#search_name_basic')));
	}

	public function Logout() {
		//Clic sur le bouton déconnexion et vérification
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("ul.nav.pull-right.top-menu li.dropdown a.dropdown-toggle")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href='index.php?module=Users&action=Logout']")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('h2.form-signin-heading')));
		
	}
}
?>
