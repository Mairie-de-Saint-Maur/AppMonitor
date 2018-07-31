<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//              classe de test applicatif iPolice               //
//                                                              //
//                   Camus 06-07-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class iPolice extends scenario {
	
	protected $driver = null;
	
   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login', 'Action', 'Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://sis.edicia.fr/stmaurdesfosses/?edicia');
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".zoneMilieu")));
	}

	public function Login() {
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#nomUtilisateur")))->clear();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#nomUtilisateur")))->sendKeys("admindsi");
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#mdpUtilisateur")))->clear();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#mdpUtilisateur")))->sendKeys("#####");
		
		$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
		
		sleep(2);
		//On vérifie s'il n'y a pas une popup avec case à cocher (forcer la connexion)
		if (count($this->driver->findElements(WebDriverBy::cssSelector("#cocheforce"))) > 0){
			
			// Saisie du login et du mot de passe puis validation
			$this->driver->findElement(WebDriverBy::cssSelector("#cocheforce"))->click();
			$this->driver->findElement(WebDriverBy::cssSelector("button#btnforce"))->click();
			
		}

		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".bandeau_titre")));
	}
   
	public function Action() {
		//On récup le premier lien dans la menu
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".menuM ul li:first-child")))->click();
		//Main courante
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".menuM ul li:first-child ul a:first-child li")))->click();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".titreEcran")))->click();
		//Fermeture d'onglet
		while(count($this->driver->findElements(WebDriverBy::cssSelector(".onglets span img[src$='fermer.gif']"))) > 0){
			$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".onglets span img[src$='fermer.gif']")))->click();
			$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#divEnglobante .MB_alert input.MB_focusable")))->click();
		}
		
		sleep(1);
		
		//Dossiers
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector(".menuM ul li:first-child")))->click();
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".menuM ul li:first-child ul a:nth-child(2) li")))->click();
		
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".titreEcran")))->click();
		//Fermeture d'onglet
		while(count($this->driver->findElements(WebDriverBy::cssSelector(".onglets span img[src$='fermer.gif']"))) > 0){
			$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".onglets span img[src$='fermer.gif']")))->click();
			$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#divEnglobante .MB_alert input.MB_focusable")))->click();
		}
		
	}

	public function Logout() {
		//SSO donc pas de déconnexion
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#btnDeconnection")));
	}
}
?>
