<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     classe de test applicatif Agora                          //
//                                                              //
//                   Blaise 04-05-2018   V1.0                   //
//                                                              //
//////////////////////////////////////////////////////////////////


use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

require_once('vendor/autoload.php');

class Agora extends scenario {
	
	protected $driver = null;

	function __construct($driver) {
		$this->driver = $driver;
		$this->steps = ['Home','Login','Action','Logout'];
	}

   public function Home() {
      // Ouverture de la page d'accueil de l'application
      $this->driver->get('http://10.51.0.8/agora/pck_security.home');
   }


   public function Login() {
      // On cherche les 3 boutons colorés des 3 domaines d'Agora
      // puis on clique aveuglément sur le deuxième 
      $elements = $this->driver->findElements(WebDriverBy::className('dock-item'));
      $nbElements = count($elements);
      if ($nbElements <> 3)
         fin("On attendait 3 disques clicables, on en a obtenu $nbElements.\n");
      else
         $elements[0]->click();

      // On attend l'affichage du bloc de login
      $element = $this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('p_login')));

      // Saisie du login et du mot de passe puis validation
      $this->driver->findElement(WebDriverBy::name('p_login'))->sendKeys('Tdsi');
      $this->driver->findElement(WebDriverBy::name('p_pass'))->sendKeys('DSI94100');
      $this->driver->findElement(WebDriverBy::id('logIn'))->click();

      //$this->driver->findElement(WebDriverBy::id('logIn'))->click();

      // On attend l'affichage effectif de la première page
      $this->driver->wait()->until(WebDriverExpectedCondition::titleContains('Agor@Baby'));
      //$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::id('title', 'login')));
   }
   
   public function Action() {
      // Clic sur les items de menu
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('GESTION DE LA POPULATION')));
      $this->driver->findElement(WebDriverBy::linkText('GESTION DE LA POPULATION'))->click();

      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('FAMILLES')));
      $this->driver->findElement(WebDriverBy::linkText('FAMILLES'))->click();

      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('Rechercher')));
      $this->driver->findElement(WebDriverBy::linkText('Rechercher'))->click();
   }

   public function Logout() {
      $this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::linkText('DÉCONNEXION')));
      $this->driver->findElement(WebDriverBy::linkText('DÉCONNEXION'))->click();
   }

}
?>
