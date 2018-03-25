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

class Zimbra extends scenario {

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('https://zimbra.mairie-saint-maur.com/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('username')));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		// Saisie du login et du mot de passe puis validation
		//$driver->open("/zimbra/");
		$driver->findElement(WebDriverBy::id('username'))->clear();
		$driver->findElement(WebDriverBy::id('username'))->sendKeys("support.informatique");
		$driver->findElement(WebDriverBy::id('password'))->clear();
		$driver->findElement(WebDriverBy::id('password'))->sendKeys("Sidsi94100");
		
		$driver->findElement(WebDriverBy::cssSelector("input.ZLoginButton.DwtButton"))->click();

		//Vérification du chargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.ImgAppBanner')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();

		// clic sur lien "Réception"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#zti__main_Mail__2_textCell')))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#CHECK_MAIL_left_icon')))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.DwtLinkButtonDropDownArrowRow')))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#logOff')))->click();
	}
}
?>
