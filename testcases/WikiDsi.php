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

class WikiDsi extends scenario {

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page de connexion de l'application
		$driver->get('http://wiki.saintmaur.local/index.php/Sp%C3%A9cial:Connexion');
		
		// Vérification de la présence du formulaire de connexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('wpName1')));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('wpPassword1')));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('wpLoginAttempt')));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();
		
		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::id('wpName1'))->clear();
		$driver->findElement(WebDriverBy::id('wpName1'))->sendKeys("INFORMATIQUE-SUP");
		$driver->findElement(WebDriverBy::id('wpPassword1'))->clear();
		$driver->findElement(WebDriverBy::id('wpPassword1'))->sendKeys("Sidsi94100");
		$driver->findElement(WebDriverBy::id('wpLoginAttempt'))->click();

		// Vérification du chargement de la page et de la présence du lien sur lequel on clique ensuite
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Modifications récentes')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();

		// clic sur lien "Modifications récentes" et vérification du chargement de la page
		$driver->findElement(WebDriverBy::partialLinkText('Modifications récentes'))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Afficher les nouvelles modifications depuis le')));
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// Déconnexion par le lien Se déconnecter
		$driver->findElement(WebDriverBy::partialLinkText('Se déconnecter'))->click();
	}
}
?>
