<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: CIRIL Finances   //
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

class CirilFinances extends scenario {

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://10.0.0.52:83/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("choixAppli")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		// Saisie du login et du mot de passe puis validation
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::name("choixAppli")));
		$driver->findElement(WebDriverBy::id('identifiant'))->clear();
		$driver->findElement(WebDriverBy::id('identifiant'))->sendKeys("SMFV221");
		$driver->findElement(WebDriverBy::id('motPasse'))->clear();
		$driver->findElement(WebDriverBy::id('motPasse'))->sendKeys("glp1962");
		
		$driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//Vérification du chargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#Header_EnteteColoree')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();

		// clic sur lien "Réception"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input.bouton_input[title='Masquer le menu']")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input.bouton_input[title='Montrer le menu']")))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input[tabindex='120']")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a.menuLine[style='background-image:url(/medias/imgweb3/composants/button_quit.png);']")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input[tabindex='1510']")))->click();
	}
}
?>
