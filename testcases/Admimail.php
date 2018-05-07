<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Admimail           //
//                                                              //
//                   Camus 19-03-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Admimail extends scenario {

	protected $driver = null;

	function __construct($driver) {
		$this->driver = $driver;
		$this->steps = ['Home','Login','Action','Logout'];
	}

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://courrier-smdf.infocom94.fr');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ext-gen1139_loginIframeDom")));
	}

	public function Login() {
		//On bascule vers le contenu de l'iframe
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::id("ext-gen1139_loginIframeDom")));
		
		//On attend pour être sûr d'avoir le contenu de l'iframe
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("username")))->clear();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("password")))->clear();
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::id('username'))->sendKeys("tdsi");
		$this->driver->findElement(WebDriverBy::id('password'))->sendKeys("DSI94100");
		
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("connect")))->click();

		//Vérification du chargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a#index_menu_global_Board')));
	}
   
	public function Action() {
		// clic sur lien "Réception"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("span.ux-adminext-admimail-board-button-name")))->click();
	}

	public function Logout() {
		// Déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div#container-1012')))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a#menuitem-1026-itemEl')))->click();
	}
}
?>
