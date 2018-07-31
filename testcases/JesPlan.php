<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif JesPlan               //
//                                                              //
//                   Camus 17-05-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class JesPlan extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://planitech/saint-maur/planitech');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".login-box")));
	}

	public function Login() {
		
		//Il faut choisir une BDD dispo
		$dropdown = new WebDriverSelect($this->driver->findElement(WebDriverBy::name("application_idx")));
		$dropdown->selectByValue('2');
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('login')))->clear();
		$this->driver->findElement(WebDriverBy::name('login'))->sendKeys("Test_DSI");
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('password')))->clear();
		$this->driver->findElement(WebDriverBy::name('password'))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();
		
		//On patiente 2 secondes pour laisser le temps à une possible fenêtre modale de s'ouvrir
		$this->driver->manage()->timeouts()->implicitlyWait(2);
		
		//Une fois loggué on a parfois une fenêtre modale
		if (count($this->driver->findElements(WebDriverBy::cssSelector('.NSModalWindow'))) > 0){
			$this->driver->findElement(WebDriverBy::cssSelector(".NSModalWindow .NSButton"))->click();
		}
		
		//On attend la présence du logo JesPlan
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.header-logo')));
		
		//On replace le timeout à 0 secondes
		$this->driver->manage()->timeouts()->implicitlyWait(0);
	}
   
	public function Action() {
		// clic
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".mainmenu-item-leaf")))->click();
		//Recherche avancée
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".XButtonTabCell:not(.active)")))->click();
		
	}

	public function Logout() {
		//Reload permet de se déconnecter
		$this->driver->navigate()->refresh();
		//Mais y'a une popup de confirmation
		$this->driver->wait()->until(WebDriverExpectedCondition::alertIsPresent());
		//On accepte
		$this->driver->switchTo()->alert()->accept();
		// Vérification de la déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('login')));
	}
}
?>
