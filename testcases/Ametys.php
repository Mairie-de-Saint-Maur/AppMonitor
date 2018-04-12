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

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://cms-intranet.mairie-saint-maur.com/intranet/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".login-inner.login-credential-provider")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::id('Username'))->clear();
		$driver->findElement(WebDriverBy::id('Username'))->sendKeys("test_applicatif");
		$driver->findElement(WebDriverBy::id('Password'))->clear();
		$driver->findElement(WebDriverBy::id('Password'))->sendKeys("r8aSPVSU5ozQDWK2mk6q");
		
		$driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();
		
		//Fermeture / ouverture du panneau latéral "plan du site"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		
		//Rechargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2506")))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// clic sur le menu utilisateur
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("button-5823")))->click();
		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("menuitem-5695-itemEl")))->click();
	}
}
?>
