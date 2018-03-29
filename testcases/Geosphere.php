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

class Geosphere extends scenario {

   function __construct($driver) {
      global $mail;

      parent::__construct($driver);
      $mail->addAddress('camus.lejarre@mairie-saint-maur.com', 'Camus Lejarre');
   }

	public function gohome() {
		$driver = $this->driver;
		parent::goHome();

		// Ouverture de la page d'accueil de l'application
		$driver->get('http://172.24.1.32/adscs/Login.aspx');
		
		//Le site va rediriger vers la page de login du SSO
		// Vérification de la présence du formulaire
		$driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".fondPortail")));
	}

	public function Login() {
		$driver = $this->driver;
		parent::Login();

		//On rentre dans la frame login:
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("input#RadTextBoxLogin")));
		
		// Saisie du login et du mot de passe puis validation
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxLogin"))->clear();
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxLogin"))->sendKeys("SMDFINFORMATIQUE");
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxMDP"))->clear();
		$driver->findElement(WebDriverBy::cssSelector("input#RadTextBoxMDP"))->sendKeys("");
		
		$driver->findElement(WebDriverBy::cssSelector("button[type=submit]"))->click();

		//Vérification du chargement de la page
		$driver->switchTo()->defaultContent();
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=bandeauHaut]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=bandeauHaut]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector('.bandeauuser')));
	}
   
	public function Action() {
		$driver = $this->driver;
		parent::Action();
		
		//Navigation dans les frames jusqu'au lien
		$driver->switchTo()->defaultContent();
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=menu]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=menu]")));
		
		// clic sur lien "Réception"
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("form[name=loginForm] td.menuLnk a[href='javascript:f_redirectSeancesList();']")))->click();
		
		//Vérification du chargement
		//Navigation dans les frames jusqu'à l'élément
		$driver->switchTo()->defaultContent();
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=main]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=main]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=action]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=action]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector(".actionBtn")));
	}

	public function Logout() {
		$driver = $this->driver;
		parent::Logout();

		//Navigation dans les frames jusqu'au lien
		$driver->switchTo()->defaultContent();
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=work]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=work]")));
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=menu]")));
		$driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector("frame[name=menu]")));
		// Déconnexion -> On trouve l'élément dont le lien commence par ...
		$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("a[href^='javascript:top.login.bandeauHaut.setDeconnection']")))->click();
	}
}
?>
