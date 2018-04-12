<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Géosphère        //
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

class Geosphere extends scenario {

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://172.24.1.32/adscs/Login.aspx');
		
		//Le site va rediriger vers la page de login du SSO
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".fondPortail")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();
		
		//On rentre dans la frame login:
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#RadTextBoxLogin")));
		
		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxLogin"))->clear();
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxLogin"))->sendKeys("SMDFINFORMATIQUE");
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxMDP"))->clear();
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxMDP"))->sendKeys("VFDMOE2016");
		
		$driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//Vérification du chargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("iframeADS")));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();
		
		//On charge à la place le contenu de la frame
		$driver->get('http://172.24.1.32/adscs/Default.aspx');
		
		//On affiche le menu d'abord
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("RadDockActions_C_HyperlinkRechercher")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".outlooktop2")));
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		//On affiche le menu d'abord
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".RadMenu")));
		$driver->findElement(WebDriverBy::cssSelector("ul.rmRootGroup.rmHorizontal > li.rmItem.rmLast"))->click();
		
		//on clique sur déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".menuUser")))->click();
	}
}
?>
