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
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('userId')))->clear();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('userId')))->sendKeys("tech");
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('password')))->clear();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('password')))->sendKeys("saintmaur2015?");
		
		//Bouton connexion "s'identifier"
		current($this->driver->findElements(WebDriverBy::cssSelector(".x-btn-center")))->click();
		
		//On attend, puis on vérifie si une popup ne s'est pas ouverte parce que l'utilisateur est resté connecté
		sleep(2);
		$el = $this->driver->findElements(WebDriverBy::xpath("//button[text()[contains(.,'connecter')]]"));
		if (count($el) >= 1) {
			current($el)->click();
		}
		
		//Affichage de la page d'accueil ?
		$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector("img[src='http://asset.keepeek.com/permalinks/domain1009/2013/03/25/24-3-Baobab-home.jpg']")));
		
	}
   
	public function Action() {
		//Fermeture / ouverture du panneau latéral "plan du site"
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath("//button[text()[contains(.,'Paniers')]]")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath("//button[text()[contains(.,'Utilisateurs')]]")))->click();
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath("//button[text()[contains(.,'Bureau')]]")))->click();
		
		//Rechargement de la page
		$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector("img[src='http://asset.keepeek.com/permalinks/domain1009/2013/03/25/24-3-Baobab-home.jpg']")));
	}

	public function Logout() {
		sleep(2);
		
		//$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath("//button[text()[contains(.,'Revenir')]]]")))->click();
		
		$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector("table.kpk-top-bar-user tbody tr td.x-btn-center button")))->click();
		// Déconnexion
		$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::linkText("Déconnexion")))->click();
		
	}
}
?>
