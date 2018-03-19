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

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('https://courrier-smdf.infocom94.fr');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ext-gen1139_loginIframeDom")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		// Saisie du login et du mot de passe puis validation
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::id("ext-gen1139_loginIframeDom")));
		$driver->findElement(WebDriverBy::id('username'))->clear();
		$driver->findElement(WebDriverBy::id('username'))->sendKeys("clejarre");
		$driver->findElement(WebDriverBy::id('password'))->clear();
		$driver->findElement(WebDriverBy::id('password'))->sendKeys("04Madlb83");
		
		$driver->findElement(WebDriverBy::id("connect"))->click();

		//Vérification du chargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a#index_menu_global_Board')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();

		// clic sur lien "Réception"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("span.ux-adminext-admimail-board-button-name")))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div#container-1012')))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('a#menuitem-1026-itemEl')))->click();
	}
}
?>
