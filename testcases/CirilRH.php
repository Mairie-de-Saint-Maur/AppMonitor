<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: CIRIL RH         //
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

class CirilRH extends scenario {

	protected $driver = null;

	function __construct($driver) {
		$this->driver = $driver;
		$this->steps = ['Home','Login','Action','Logout'];
	}

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://10.0.0.52:83/');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("choixAppli")));
	}

	public function Login() {
		// Saisie du login et du mot de passe puis validation
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::name("choixAppli")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('identifiant')))->clear();
		$this->driver->findElement(WebDriverBy::id('identifiant'))->sendKeys("smdfdsi");
		$this->driver->findElement(WebDriverBy::id('motPasse'))->clear();
		$this->driver->findElement(WebDriverBy::id('motPasse'))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//GESTION DE LA POPUP
		
		//On récupère l'ID de la fenêtre principale
		$mainWindowHandler = $this->driver->getWindowHandle();

		//On attend 1 sec que la popup s'ouvre
		sleep(1);
		//On récpère la liste des fenêtres
		$allWindowHandlers = $this->driver->getWindowHandles();
		
		//On boucle sur les fenêtres en fermant celle qui ne sont pas la fenêtre principale
		foreach($allWindowHandlers as $currWindowHandler) {
			if ($currWindowHandler != $mainWindowHandler) {
				$this->driver->switchTo()->window($currWindowHandler);
				$this->driver->close();
			}
		}
		$this->driver->switchTo()->window($mainWindowHandler);
	}
   
	public function Action() {
		// clic sur lien "Réception"
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::name("choixAppli")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("burger_button")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("notifications_button")))->click();
	}

	public function Logout() {
		// Déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("identite_button")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("deconnexion")))->click();
	}
}
?>
