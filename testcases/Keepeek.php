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
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://phototheque.saintmaur.local/');
		
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name("userId")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::name('userId'))->clear();
		$driver->findElement(WebDriverBy::name('userId'))->sendKeys("tech");
		$driver->findElement(WebDriverBy::name('password'))->clear();
		$driver->findElement(WebDriverBy::name('password'))->sendKeys("saintmaur2015?");
		
		current($driver->findElements(WebDriverBy::cssSelector(".x-btn-center")))->click();
		
		//Chargement de la page d'accueil
		//On vérifie si une popup ne s'est pas ouverte parce que l'utilisateur est resté connecté
		$driver->findElement(WebDriverBy::xpath("//button[text()[contains(.,'Se connecter')]]"));
		
		exit;
		
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("img[src='http://asset.keepeek.com/permalinks/domain1009/2013/03/25/24-3-Baobab-home.jpg']")));
		exit;
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();
		
		//Fermeture / ouverture du panneau latéral "plan du site"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2005")))->click();
		
		//Rechargement de la page
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("ametys-ribbon-button-2506")))->click();
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		// clic sur le menu utilisateur
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("button-5823")))->click();
		// Déconnexion
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id("menuitem-5695-itemEl")))->click();
	}
}
?>
