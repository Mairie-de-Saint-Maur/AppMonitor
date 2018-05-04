<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif de référence: DuoNet           //
//                                                              //
//                   Blaise 19-03-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class DuoNet extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['Home','Login','Action','Logout'];
   }

   public function Home() {
      // Ouverture de la page d'accueil de l'application
      $this->driver->get('http://10.0.0.81/');

      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('txtLogin')));
   }


   public function Login() {
      // On accpete les cookies
      $this->driver->findElement(WebDriverBy::cssSelector('button'))->click();
      
      // Saisie du login et du mot de passe puis validation
      $this->driver->findElement(WebDriverBy::id('txtLogin'))->clear();
      $this->driver->findElement(WebDriverBy::id('txtLogin'))->sendKeys('frederic.guillet@mairie-saint-maur.com');
      $this->driver->findElement(WebDriverBy::id('txtPassword'))->clear();
      $this->driver->findElement(WebDriverBy::id('txtPassword'))->sendKeys('frederic.guillet');
     
      $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('btnConnect')))->click();
   }
   
   public function Action() {
      // Clic sur menu liste des salles
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ctl00_contMainmenu_dlMainMenu_ctl06_hlk")))->click();
      $this->driver->findElement(WebDriverBy::xpath("//div[@id='ctl00_chpMain_grdRes_ob_grdResBodyContainer']/table/tbody/tr[4]/td[2]/div"))->click();

      // Clic sur menu paramètres
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('ctl00_hlkParams')))->click();
   }

   public function Logout() {
      // Déconnexion
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('ctl00_hlkDisconnect')))->click();
   }

}
?>
