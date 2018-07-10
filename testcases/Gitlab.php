<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Gitlab           //
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

class Gitlab extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://gitlab.saintmaur.local:9091/');
		
		// Vérification de la présence du formulaire de connexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('username')));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('password')));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Standard')));
	}

	public function Login() {
		// On se met dans l'onglet d'authentification "Standard"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Standard')))->click();
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::id('user_login'))->clear();
		$this->driver->findElement(WebDriverBy::id('user_login'))->sendKeys("support.informatique");
		$this->driver->findElement(WebDriverBy::id('user_password'))->clear();
		$this->driver->findElement(WebDriverBy::id('user_password'))->sendKeys("azerty94100");
		$this->driver->findElement(WebDriverBy::cssSelector('div.submit-container.move-submit-down input'))->click();

		// Vérification du chargement de la page et de la présence du lien sur lequel on clique ensuite
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Explore public projects')));
	}
   
	public function Action() {
		// clic sur lien "Explore public projects"
		$this->driver->findElement(WebDriverBy::partialLinkText('Explore public projects'))->click();
	}

	public function Logout() {
		// Déconnexion par le menu déroulant et Sign out
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a.header-user-dropdown-toggle')));
		$this->driver->findElement(WebDriverBy::cssSelector('a.header-user-dropdown-toggle'))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a.sign-out-link')));
		$this->driver->findElement(WebDriverBy::cssSelector('a.sign-out-link'))->click();
	}
}
?>
