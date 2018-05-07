<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Wiki DSI         //
//                                                              //
//            STEPHAN Hugo 24-03-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');
//
class WikiDsi extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page de connexion de l'application
		$this->driver->get('http://wiki.saintmaur.local/index.php/Sp%C3%A9cial:Connexion');
		
		// Vérification de la présence du formulaire de connexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('wpName1')));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('wpPassword1')));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('wpLoginAttempt')));
	}

	public function Login() {
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::id('wpName1'))->clear();
		$this->driver->findElement(WebDriverBy::id('wpName1'))->sendKeys("INFORMATIQUE-SUP");
		$this->driver->findElement(WebDriverBy::id('wpPassword1'))->clear();
		$this->driver->findElement(WebDriverBy::id('wpPassword1'))->sendKeys("Sidsi94100");
		$this->driver->findElement(WebDriverBy::id('wpLoginAttempt'))->click();

		// Vérification du chargement de la page et de la présence du lien sur lequel on clique ensuite
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Modifications récentes')));
	}
   
	public function Action() {
		// clic sur lien "Modifications récentes" et vérification du chargement de la page
		$this->driver->findElement(WebDriverBy::partialLinkText('Modifications récentes'))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Afficher les nouvelles modifications depuis le')));
	}

	public function Logout() {
		// Déconnexion par le lien Se déconnecter
		$this->driver->findElement(WebDriverBy::partialLinkText('Se déconnecter'))->click();
	}
}
?>
