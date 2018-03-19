<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: Zimbra           //
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

class Artotheque extends scenario {

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('https://zimbra.mairie-saint-maur.com/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('username')));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		// Saisie du login et du mot de passe puis validation
		//$driver->open("/zimbra/");
		$driver->type("id=username", "support.informatique");
		$driver->type("id=password", "Sidsi94100");
		$driver->click("css=input.ZLoginButton.DwtButton");

		//Vérification du chargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.ImgAppBanner')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();

		// clic sur lien "Réception"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#zti__main_Mail__2_textCell > span')))->click();
		//Clic sur l'onglet calendrier
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#zb__App__Calendar_title')))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('css=#logOff')))->click();
	}
}
?>
