<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Gitlab           //
//                                                              //
//                   Hugo 24-03-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Gitlab extends scenario {

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://gitlab.saintmaur.local:9091/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('username')));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('password')));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Standard')));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::partialLinkText('Standard')))->click();
		
		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::id('user_login'))->clear();
		$driver->findElement(WebDriverBy::id('user_login'))->sendKeys("support.informatique");
		$driver->findElement(WebDriverBy::id('user_password'))->clear();
		$driver->findElement(WebDriverBy::cssSelector('div.submit-container.move-submit-down input'))->click();

		//Vérification du chargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.blank-state-icon')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();

		// clic sur lien "Réception"
		$driver->findElement(WebDriverBy::partialLinkText('Explore public projects'))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// Déconnexion
		$driver->findElement(WebDriverBy::partialLinkText('Sign out'))->click();
	}
}
?>
