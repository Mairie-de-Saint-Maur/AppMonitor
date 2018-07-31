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

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://172.24.1.32/adscs/Login.aspx');
		
		//Le site va rediriger vers la page de login du SSO
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".fondPortail")));
	}

	public function Login() {
		//On rentre dans la frame login:
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#RadTextBoxLogin")));
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxLogin"))->clear();
		$this->driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxLogin"))->sendKeys("SMDFINFORMATIQUE");
		$this->driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxMDP"))->clear();
		$this->driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxMDP"))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//Vérification du chargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("iframeADS")));
	}
   
	public function Action() {
		//On charge à la place le contenu de la frame
		$this->driver->get('http://172.24.1.32/adscs/Default.aspx');
		
		//On affiche le menu d'abord
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("RadDockActions_C_HyperlinkRechercher")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".outlooktop2")));
	}

	public function Logout() {
		//On affiche le menu d'abord
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".RadMenu")));
		$this->driver->findElement(WebDriverBy::cssSelector("ul.rmRootGroup.rmHorizontal > li.rmItem.rmLast"))->click();
		
		//on clique sur déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".menuUser")))->click();
	}
}
?>
