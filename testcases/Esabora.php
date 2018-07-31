<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: AIRS Delib       //
//                                                              //
//                   Camus 26-03-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Esabora extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://esabora.saintmaur.local/');
		
		//Le site va rediriger vers la page de login du SSO
		// Vérification de la présence du formulaire
		//$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("div#logo_app")));
	}

	public function Login() {
		
		
		//On vérifie si on arrive sur le login du SSO ou si on est entré sans frappé
		if (count($this->driver->findElements(WebDriverBy::cssSelector("input#username.form-element"))) > 0){
			
			// Saisie du login et du mot de passe puis validation
			$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->clear();
			$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->sendKeys("lejarre-cam");
			$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->clear();
			$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->sendKeys("#####");
			
			$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();
			
		}
		
		$allWindowHandlers = $this->driver->getWindowHandles();
		//var_dump($allWindowHandlers);
		foreach($allWindowHandlers as $handle){
			$this->driver->switchTo()->window($handle);
			if (count($this->driver->findElements(WebDriverBy::cssSelector(".logo"))) > 0){
				$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".logo")));
			}
		}
	}
   
	public function Action() {
		//Recherche N° Dossiers
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#btRDossiers")))->click();
		
		//Retour
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#btRetour")))->click();
	}

	public function Logout() {
		//Quitter
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#btQuitter")))->click();
		
		//On recherche la fenêtre avec le bloc de connexion
		$allWindowHandlers = $this->driver->getWindowHandles();
		//var_dump($allWindowHandlers);
		foreach($allWindowHandlers as $handle){
			//On switche dessus pour que le close la ferme
			$this->driver->switchTo()->window($handle);
			if (count($this->driver->findElements(WebDriverBy::cssSelector("#logobox"))) > 0){
				$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#logobox")));
			}
		}
	}
}
?>
