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

   public function gohome() {
      $driver = $this->driver;
      parent::goHome();

      // Ouverture de la page d'accueil de l'application
      $driver->get('http://10.0.0.81/');

      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('txtLogin')));
   }


   public function Login() {
      $driver = $this->driver;
      parent::Login();
      
      // On accpete les cookies
      $driver->findElement(WebDriverBy::cssSelector('button'))->click();
      
      // Saisie du login et du mot de passe puis validation
      $driver->findElement(WebDriverBy::id('txtLogin'))->clear();
      $driver->findElement(WebDriverBy::id('txtLogin'))->sendKeys('frederic.guillet@mairie-saint-maur.com');
      $driver->findElement(WebDriverBy::id('txtPassword'))->clear();
      $driver->findElement(WebDriverBy::id('txtPassword'))->sendKeys('frederic.guillet');
     
      $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('btnConnect')))->click();
   }
   
   public function Action() {
      $driver = $this->driver;
      parent::Action();

      // Clic sur menu liste des salles
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ctl00_contMainmenu_dlMainMenu_ctl06_hlk")))->click();
      $driver->findElement(WebDriverBy::xpath("//div[@id='ctl00_chpMain_grdRes_ob_grdResBodyContainer']/table/tbody/tr[4]/td[2]/div"))->click();

      // Clic sur menu paramètres
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('ctl00_hlkParams')))->click();
   }

   public function Logout() {
      $driver = $this->driver;
      parent::Logout();

      // Déconnexion
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('ctl00_hlkDisconnect')))->click();
   }

}
?>
