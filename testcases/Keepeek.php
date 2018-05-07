<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//       classe de test applicatif de référence: Keepeek        //
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

class Keepeek extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://phototheque.saintmaur.local/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("userId")));
	}

	public function Login() {
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::name('userId'))->clear();
		$this->driver->findElement(WebDriverBy::name('userId'))->sendKeys("tech");
		$this->driver->findElement(WebDriverBy::name('password'))->clear();
		$this->driver->findElement(WebDriverBy::name('password'))->sendKeys("saintmaur2015?");
		
		current($this->driver->findElements(WebDriverBy::cssSelector(".x-btn-center")))->click();
		
		//Chargement de la page d'accueil
		//On vérifie si une popup ne s'est pas ouverte parce que l'utilisateur est resté connecté
		$this->driver->findElement(WebDriverBy::xpath("//button[text()[contains(.,'Se connecter')]]"));
		
		exit;
		
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("img[src='http://asset.keepeek.com/permalinks/domain1009/2013/03/25/24-3-Baobab-home.jpg']")));
		exit;
	}
   
	public function Action() {
		//Fermeture / ouverture du panneau latéral "plan du site"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		
		//Rechargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2506")))->click();
	}

	public function Logout() {
		// clic sur le menu utilisateur
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("button-5823")))->click();
		// Déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("menuitem-5695-itemEl")))->click();
	}
}
?>
