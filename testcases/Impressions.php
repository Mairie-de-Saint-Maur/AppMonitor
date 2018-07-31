<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Impressions      //
//                                                              //
//                   Camus 04-04-2018   V0.2                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Impressions extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://impression.mairie-saint-maur.com/app_imprimerie/');
		
		//Le site va rediriger vers la page de login du SSO
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".navbar-brand")));
	}

	public function Login() {
		//On rentre dans la frame login:
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#form_authentification")));
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::cssSelector("input#login"))->clear();
		$this->driver->findElement(WebDriverBy::cssSelector("input#login"))->sendKeys("thomas.devos");
		$this->driver->findElement(WebDriverBy::cssSelector("input#mdp"))->clear();
		$this->driver->findElement(WebDriverBy::cssSelector("input#mdp"))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a.btn.btn-success")));
	}
   
	public function Action() {
		//On affiche le menu d'abord
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a.btn.btn-success")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("imprBackHome")))->click();
	}

	public function Logout() {
		//On affiche le menu d'abord
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href='index.php?ctrl=AuthentificationCtrl&action=deconnexion']")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("login")));
	}
}
?>