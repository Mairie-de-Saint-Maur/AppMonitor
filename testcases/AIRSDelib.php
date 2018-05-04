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

class AIRSDelib extends scenario {

	protected $driver = null;
	

   function __construct($driver) {
	   $this->driver = $driver;
	   $this->steps = ['gohome','Login','Action','Logout'];
   }

	public function gohome() {
		// Ouverture de la page d'accueil de l'application
		$this->driver->get('https://airsdelib.infocom94.fr:9290/delib/index.html');
		
		// Vérification de la présence du formulaire
		$this->driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=login]")));
	}

	public function Login() {
		//On rentre dans la frame login:
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=login]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=login]")));
		//Puis dans la frame general
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("frame[name=general]")));
		$this->driver->switchTo()->frame($this->driver->findElement(WebDriverBy::cssSelector("frame[name=general]")));
		
		// Saisie du login et du mot de passe puis validation
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('login')))->clear();
		$this->driver->findElement(WebDriverBy::name('login'))->sendKeys("ADM");
		$this->driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('password')))->clear();
		$this->driver->findElement(WebDriverBy::name('password'))->sendKeys("SmdfAdm!n94");
		
		$this->driver->findElement(WebDriverBy::cssSelector("input[type=submit]"))->click();

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
