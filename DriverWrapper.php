<?php

//Facebook Webdriver
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

class DriverWrapper {
	
	private $NiceMail = null;
	
	private $driver = null;
	
	private $error = 0;

	// Execution du navigateur sur le serveur défini par la conf
	private $host ; #=  Config::SELENIUM_HOST;
	
	private $connection_timeout ; # =  Config::CONNECT_TIMEOUT;
	private $request_timeout ; #=  Config::QUERY_TIMEOUT;
	
	private $timeLast = 0;
	//Tableau contenant les temps pour chaque step
	private $times = array();
	
	
	//Getters/Setters pour gérer le temps écoulé
	function getTimeLast(){
		return $this->timeLast;
	}

	function setTimeLast($time){
		$this->timeLast = $time;
	}
	function getTimes($step = null){
		if ($step){
			return $this->times[$step];
		}else{
			return $this->times;
		}
	}

	function setTimes(array $times){
		$this->times = $times;
	}
	
	
	
	function setDriver($driver){
		$this->driver = $driver;
	}
	
	function getDriver(){
		return $this->driver;
	}
	
	function getNiceMail(){
		return $this->NiceMail;
	}
	
	function setHost($host){
		$this->host = $host;
	}
	
	function setConnectionTimeout($time){
		$this->connection_timeout = $time;
	}
	
	function setRequestTimeout($time){
		$this->request_timeout = $time;
	}
	
	function setError($err){
		$this->error = $err;
	}
	
	function addError($err){
		$this->error += $err;
	}
	
	function getError(){
		return $this->error;
	}
	
	function __construct(NiceMail $mail = null)
	{
		$this->host =  Config::$SELENIUM_HOST;

        	$this->connection_timeout =  Config::$CONNECT_TIMEOUT;
		$this->request_timeout =  Config::$QUERY_TIMEOUT;		

		if($mail === null) $mail = new NiceMail();
		
		Console("Initialisation du \e[1;33mWebDriver\e[0m\n");
		$this->NiceMail = $mail;
	
	///////////////////////////////////////////////////////////////////
	// Initialise et paramètre le navigateur pour la simulation      //
	///////////////////////////////////////////////////////////////////
	
		// Choix du navigateur
		$options = new ChromeOptions();
		
		// Data dir unique
		$dirname = "/tmp/chromedata." . date("Ymd-h:i:s") ;
		$options->addArguments(array("--start-maximized","--incognito","--headless","--user-data-dir=".$dirname));// "--no-sandbox","--disable-setuid-sandbox"));
		$capabilities = DesiredCapabilities::chrome();
		#$capabilities = $options.ToCapabilities();
		$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
	 	$capabilities->setCapability("chromeOptions", array(
			"args" => array(
			"--start-maximized",
			"--headless",
			"--no-default-browser-check",
			"--user-data-dir=".$dirname,
			"--incognito"
			)
		));
				

		$this->startDriver($capabilities);
	}
	
	function startDriver($capabilities){
		
		// Lancement du navigateur sur le client cible, timeout de 10 secondes
		Console("Lancement du navigateur \e[1;33mChrome\e[0m\n");
		try
		{
		   $this->driver = RemoteWebDriver::create($this->host, $capabilities, $this->connection_timeout, $this->request_timeout);
		} 
		catch(Exception $e) {
		   fwrite(STDERR, "Première tentative de lancement du navigateur échouée\n");
		   fwrite(STDERR, "$e->getMessage()");
			Console("\n\e[0;31m /!\ ERREUR \e[0m Impossible de lancer le navigateur (1ère tentative)\n");
		   $this->NiceMail->Subject = "Première tentative de lancement du navigateur échouée";
		   $this->NiceMail->addBody("$e->getMessage()");
		}   
		
		//On compte le nombre de fenêtres ouvertes pour voir si Chrome s'est lancé
		$allWindowHandlers = count($this->driver->getWindowHandles());
		
		// Si on n'a pas réussi à lancer le navigateur, on essaye encore une fois
		if (!isset($this->driver) or $allWindowHandlers < 1){
		   try {
			  $this->driver = RemoteWebDriver::create($this->host, $capabilities, $connection_timeout, $request_timeout);
		   }
		   catch(Exception $e) {
			  fwrite(STDERR, "Deuxième tentative de lancement du navigateur échouée\n");
			  fwrite(STDERR, "$e->getMessage()");
			  Console("\n\e[0;31m /!\ ERREUR \e[0m Impossible de lancer le navigateur (2ème tentative)\n");
			  $this->NiceMail->Subject = "Deuxième tentative de lancement du navigateur échouée";
			  $this->NiceMail->addBody("$e->getMessage()");
			  $this->error += 2;
			}
		}

		//On compte le nombre de fenêtres ouvertes pour voir si Chrome s'est lancé
		$allWindowHandlers = count($this->driver->getWindowHandles());
		
		// A t on réussi à lancer le navigateur? Si non on arrête le script ici
		if (!isset($this->driver) or $allWindowHandlers < 1){
			$this->fin(1, "\nTests Selenium \e[0;31mKO, lancement navigateur impossible !\e[0m\n");
		}else{
			Console("[\e[0;32mOK\e[0m - Fenêtres : $allWindowHandlers]\n\n");
		}
	}
		
	///////////////////////////////////////////////////////////////////
	//  Sortie propre                                                //
	///////////////////////////////////////////////////////////////////

	function fin($exit_code=0, $message='fin de simulation')
	{
		Console("\n\e[1;34m$message\e[0m\n");

		// Si le script a échoué 
		if ($exit_code > 0) {
			$exit_code = max($this->error, $exit_code);
		}

		// Ferme le driver (encore)
		if (isset($this->driver))
		{
			$this->closeDriver($this->driver);
		}	  
		exit($exit_code);
	}

	///////////////////////////////////////////////////////////////////
	// Calcul du temps d'execution de chaque étape                   //
	///////////////////////////////////////////////////////////////////
	function logTime($step = null)
	{
	   $timeCurrent = round(microtime(true) * 1000);
	   $elapsed = $timeCurrent - $this->timeLast;
	   $this->timeLast = $timeCurrent;
	   if($step) $this->times[$step] = $elapsed;
	   return $elapsed;
	}


	///////////////////////////////////////////////////////////////////
	// Prend un snapshot de l'état courant du test en indiquant l'heure et l'étape
	///////////////////////////////////////////////////////////////////
	function takeSnapshot($step, $name)
	{
		if (isset($this->driver)) {
			$screenshot =  Config::$SCREENSHOT_DIR."screenshot-$name-$step-". date("Y-m-d_H-i-s") . ".png";
			try {
				$this->driver->takeScreenshot($screenshot);
			}
			catch(Exception $e) {
				fwrite(STDERR, "Impossible de prendre une copie d'écran\n");
				$this->NiceMail->addBody("<h3 class='error'>Impossible de prendre une copie d'écran.</h3><p>($screenshot)<br>".$e->getMessage()."</p>");
				$this->error += 4;
				return $this->error;
			}
			$this->NiceMail->addAttachment($screenshot);
		}
		return 0;
	}

	function closeDriver()
	{		
		$this->driver->close();
		$this->driver->quit();
	}
}
?>
