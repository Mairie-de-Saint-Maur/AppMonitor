<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: GeoDP            //
//                                                              //
//                  Blaise 18-04-2018   V0.1                    //
//                                                              //
//////////////////////////////////////////////////////////////////

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class GeoDP extends scenario {

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('blaise.thauvin@mairie-saint-maur.com', 'Blaise Thauvin');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://10.0.0.82/geodp.smdf/index.htm');
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::name("mainFrame")));	
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("Txt_Identifiant")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();
		
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("Txt_Identifiant")));
		
		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::name("Txt_Identifiant"))->clear();
		$driver->findElement(WebDriverBy::name("Txt_Identifiant"))->sendKeys("TEST");
		$driver->findElement(WebDriverBy::name("Txt_MotDePasse"))->clear();
		$driver->findElement(WebDriverBy::name("Txt_MotDePasse"))->sendKeys("DSISTMAUR1");
		$driver->findElement(WebDriverBy::linkText("Valider"))->click();
            
                // On attend l'affichage des boutons des deux modules ODP et TLPE
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::name("mainFrame")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("image9")));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();
		
                //On clique sur ODP puis quelques menus
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("image9")))->click();

	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		//clic sur menu déconnexion
                //$driver->switchTo()->window($driver->findElement(WebDriverBy::name("leftFrame")));
                $driver->switchTo()->window($driver->findElement(WebDriverBy::cssSelector("frame[name=leftFrame]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#image18 > div.div_menu.div_menu_survol")))->click();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=mainFrame]")));
                $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("Txt_Identifiant")));
	}
}
?>
