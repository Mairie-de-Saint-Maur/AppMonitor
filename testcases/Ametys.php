<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//  classe de test applicatif de référence: Ametys - Intranet   //
//                                                              //
//                   Camus 04-04-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Ametys extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://cms-intranet.mairie-saint-maur.com/intranet/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".login-inner.login-credential-provider")));
	}

	public function Login() {
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Username')))->clear();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Username')))->sendKeys("test_applicatif");
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Password')))->clear();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('Password')))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
	}
   
	public function Action() {
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")));
		/*
		//Fermeture / ouverture du panneau latéral "plan du site"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		
		//Rechargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2506")))->click();
		*/
	}

	public function Logout() {
		// clic sur le menu utilisateur
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("button-5823")))->click();
		// Déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("menuitem-5695-itemEl")))->click();
	}
}
?>
