<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: GeoDP            //
//                                                              //
//                  Blaise 18-04-2018   V0.1                    //
//                  Camus  23-04-2018   V0.2                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class GeoDP extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

	public function Home() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('http://10.0.0.82/geodp.smdf/index.htm');
                $this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::name("mainFrame")));	
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("Txt_Identifiant")));
	}

	public function Login() {
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("Txt_Identifiant")));
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->findElement(WebDriverBy::name("Txt_Identifiant"))->clear();
		$this->driver->findElement(WebDriverBy::name("Txt_Identifiant"))->sendKeys("TEST");
		$this->driver->findElement(WebDriverBy::name("Txt_MotDePasse"))->clear();
		$this->driver->findElement(WebDriverBy::name("Txt_MotDePasse"))->sendKeys("DSISTMAUR1");
		$this->driver->findElement(WebDriverBy::linkText("Valider"))->click();
            
                // On attend l'affichage des boutons des deux modules ODP et TLPE
                $this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::name("mainFrame")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("image9")));
	}
   
	public function Action() {
		//On clique sur ODP puis quelques menus
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("image9")))->click();

	}

	public function Logout() {
		//clic sur menu déconnexion
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::id("leftFrame")));
		$this->driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::cssSelector("a.lien_menu[href='../deconnexion.asp'")))->click();
		$this->driver->switchTo()->defaultContent();
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::id("mainFrame")));
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("Txt_Identifiant")));
	}
}
?>
