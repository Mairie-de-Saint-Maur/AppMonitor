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
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("div#logo_app")));
	}

	public function Login() {
		//On rentre dans la frame login:
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#username")));
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->clear();
		$this->driver->findElement(WebDriverBy::cssSelector("input#username"))->sendKeys("");
		$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->clear();
		$this->driver->findElement(WebDriverBy::cssSelector("input#password"))->sendKeys("");
		
		$this->driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();

		//Vérification du chargement de la page
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=bandeauHaut]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=bandeauHaut]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.bandeauuser')));
	}
   
	public function Action() {
		//Navigation dans les frames jusqu'au lien
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
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
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".actionBtn")));
	}

	public function Logout() {
		//Navigation dans les frames jusqu'au lien
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=menu]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=menu]")));
		// Déconnexion -> On trouve l'élément dont le lien commence par ...
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href^='javascript:top.login.bandeauHaut.setDeconnection']")))->click();
	}
}
?>
