<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif Agora                          //
//                                                              //
//                   Blaise 31-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Agora extends scenario {


   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
      $mail->addAddress('agora@infocom94.fr, 'Support Agora Infocom');
   }

   public function gohome() {
      $driver = $this->driver;
      parent::goHome();

      // Ouverture de la page d'accueil de l'application
      $driver->get('http://10.51.0.8/agora/pck_security.home');
   }


   public function Login() {
      $driver = $this->driver;
      parent::Login();

      // On cherche les 3 boutons colorés des 3 domaines d'Agora
      // puis on clique aveuglément sur le deuxième 
      $elements = $driver->findElements(WebDriverBy::className('dock-item'));
      $nbElements = count($elements);
      if ($nbElements <> 3)
         fin("On attendait 3 disques clicables, on en a obtenu $nbElements.\n");
      else
         $elements[0]->click();

      // On attend l'affichage du bloc de login
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('p_login')));

      // Saisie du login et du mot de passe puis validation
      $driver->findElement(WebDriverBy::name('p_login'))->sendKeys('Tdsi');
      $driver->findElement(WebDriverBy::name('p_pass'))->sendKeys('DSI94100');
      $driver->findElement(WebDriverBy::id('logIn'))->click();

      // On attend l'affichage effectif de la première page
      $driver->wait()->until(WebDriverExpectedCondition::titleContains('Agor@Baby'));
      //$driver->wait()->until(WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::id('title', 'login')));
   }
   
   public function Action() {
      $driver = $this->driver;
      parent::Action();

      // Clic sur les items de menu
      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('GESTION DE LA POPULATION')));
      $driver->findElement(WebDriverBy::linkText('GESTION DE LA POPULATION'))->click();

      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('FAMILLES')));
      $driver->findElement(WebDriverBy::linkText('FAMILLES'))->click();

      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Rechercher')));
      $driver->findElement(WebDriverBy::linkText('Rechercher'))->click();
   }

   public function Logout() {
      $driver = $this->driver;
      parent::Logout();

      $driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('DÉCONNEXION')));
      $driver->findElement(WebDriverBy::linkText('DÉCONNEXION'))->click();
   }

}
?>
