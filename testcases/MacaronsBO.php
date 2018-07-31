<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif MacaronsBO            //
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

class MacaronsBO extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://bo-zones-bleues.mairie-saint-maur.com'); //ATTENTION : version de PREPROD en attendant la publication en PROD
		
	}

	public function Login() {
		
		//On vérifie si on arrive sur le login du SSO ou si on est entré sans frappé
		if (count($this->driver->findElements(WebDriverBy::cssSelector("input#username.form-element"))) > 0){
			
			// Saisie du login et du mot de passe puis validation
			$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->clear();
			$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->sendKeys("lejarre-cam");
			$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->clear();
			$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->sendKeys("#####");
			
			$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
			
		}
		
		//Interface d'accueil
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#titre_application")));
		
	}
   
	public function Action() {
		//On clique sur les dossiers incomplets
		$this->driver->findElement(WebDriverBy::cssSelector("#logoutButton a:nth-child(3)"))->click();
		
		//On clique sur les dossiers terminés
		$this->driver->findElement(WebDriverBy::cssSelector("#logoutButton a:nth-child(4"))->click();
		
	}

	public function Logout() {
		//SSO donc pas de déconnexion
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#titre_application")));
	}
}
?>
