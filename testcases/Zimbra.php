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

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://zimbra.mairie-saint-maur.com/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('username')));
	}

	public function Login() {
		// Saisie du login et du mot de passe puis validation
		//$this->driver->open("/zimbra/");
		$this->driver->findElement(WebDriverBy::id('username'))->clear();
		$this->driver->findElement(WebDriverBy::id('username'))->sendKeys("support.informatique");
		$this->driver->findElement(WebDriverBy::id('password'))->clear();
		$this->driver->findElement(WebDriverBy::id('password'))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input.ZLoginButton.DwtButton"))->click();

		//Vérification du chargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.ImgAppBanner')));
	}
   
	public function Action() {
		// clic sur lien "Réception"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#zti__main_Mail__2_textCell')))->click();
		//$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#CHECK_MAIL_left_icon')))->click();
	}

	public function Logout() {
		// Déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('div.DwtLinkButtonDropDownArrowRow')))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('#logOff')))->click();
	}
}
?>
