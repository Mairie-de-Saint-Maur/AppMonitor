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

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function Home() {
		$driver = $this->driver;
		parent::Home();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://10.0.0.52:83/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("choixAppli")));
	}

	public function Login() {
		$driver = $this->driver;
//		parent::Login();

		// Saisie du login et du mot de passe puis validation
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::name("choixAppli")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('identifiant')))->clear();
		$driver->findElement(WebDriverBy::id('identifiant'))->sendKeys("smdfdsi");
		$driver->findElement(WebDriverBy::id('motPasse'))->clear();
		$driver->findElement(WebDriverBy::id('motPasse'))->sendKeys("Orage1752");
		
		$driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

		//GESTION DE LA POPUP
		
		//On récupère l'ID de la fenêtre principale
		$mainWindowHandler = $driver->getWindowHandle();

		//On attend 1 sec que la popup s'ouvre
		sleep(1);
		//On récpère la liste des fenêtres
		$allWindowHandlers = $driver->getWindowHandles();
		
		//On boucle sur les fenêtres en fermant celle qui ne sont pas la fenêtre principale
		foreach($allWindowHandlers as $currWindowHandler) {
			if ($currWindowHandler != $mainWindowHandler) {
				$driver->switchTo()->window($currWindowHandler);
				$driver->close();
			}
		}
		$driver->switchTo()->window($mainWindowHandler);
	}
   
	public function Action() {
		$driver = $this->driver;
//		parent::Action();

		// clic sur lien "Réception"
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::name("choixAppli")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("burger_button")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("notifications_button")))->click();
	}

	public function Logout() {
		$driver = $this->driver;
//		parent::Logout();

		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("identite_button")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id("deconnexion")))->click();
	}
}
?>
