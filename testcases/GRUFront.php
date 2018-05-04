<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif GU Front              //
//                                                              //
//                   Camus 23-04-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class GRUFront extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://capdemat-front-prp.multimediabs.com/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".avia-menu.av-main-nav-wrap")));
	}

	public function Login() {
		//parent::Login();

		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://capdemat-front-prp.multimediabs.com/connexion');
		/*
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href^='http://capdemat-front-prp.multimediabs.com/connexion'")));
		
		//On rentre dans la frame login:
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		//Puis dans la frame general
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=general]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=general]")));
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('login')))->clear();
		$this->driver->findElement(WebDriverBy::name('login'))->sendKeys("alt.r2-9ok7tvlt@yopmail.com");
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('password')))->clear();
		$this->driver->findElement(WebDriverBy::name('password'))->sendKeys("CLgru94100");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//Vérification du chargement de la page
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=bandeauHaut]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=bandeauHaut]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.bandeauuser')));
		*/
	}
   
	public function Action() {
		/*
		//parent::Action();
		
		//Navigation dans les frames jusqu'au lien
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=menu]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=menu]")));
		
		// clic sur lien "Réception"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("form[name=loginForm] td.menuLnk a[href='javascript:f_redirectSeancesList();']")))->click();
		
		//Vérification du chargement
		//Navigation dans les frames jusqu'à l'élément
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=main]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=main]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=action]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=action]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".actionBtn")));*/
	}

	public function Logout() {
		/*
		//parent::Logout();

		//Navigation dans les frames jusqu'au lien
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=menu]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=menu]")));
		// Déconnexion -> On trouve l'élément dont le lien commence par ...
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href^='javascript:top.login.bandeauHaut.setDeconnection']")))->click();
		*/
	}
}
?>
