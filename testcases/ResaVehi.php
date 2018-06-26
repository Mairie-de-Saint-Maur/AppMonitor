<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif JesPlan               //
//                                                              //
//                   Camus 17-05-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class ResaVehi extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://reservation-vehicules-x.mairie-saint-maur.com'); //ATTENTION : version de PREPROD en attendant la publication en PROD
		
	}

	public function Login() {
		
		//On vérifie si on arrive sur le login du SSO ou si on est entré sans frappé
		if (count($this->driver->findElements(WebDriverBy::cssSelector("input#username.form-element"))) > 0){
			
			// Saisie du login et du mot de passe puis validation
			$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->clear();
			$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->sendKeys("lejarre-cam");
			$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->clear();
			$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->sendKeys("04Madlb83");
			
			$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
			
		}
		
		//Interface d'accueil
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".navbar-brand")));
		
	}
   
	public function Action() {
		//On récup le premier lien dans la nav-bar
		$this->driver->findElement(WebDriverBy::cssSelector("ul.nav.navbar-nav.navbar-right li:first-child"))->click();
		
		//On passe sur la résa de vélos
		$this->driver->findElement(WebDriverBy::cssSelector("ul.nav.nav-tabs.tab-onglet li.animated"))->click();
		
	}

	public function Logout() {
		//SSO donc pas de déconnexion
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".navbar-brand")));
	}
}
?>
