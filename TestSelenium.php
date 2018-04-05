#!/usr/bin/php
<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif pour Selenium 2                //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////

require_once('vendor/autoload.php');

//Fichier de configuration
require_once('config.php');

//Facebook Webdriver
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Chrome\ChromeOptions;

///////////////////////////////////////////////////////////////////
//    Gestion des exceptions                                     //
///////////////////////////////////////////////////////////////////

//Exceptions "imprévues"
function exception_handler($exception)
{
   global $scenario, $mail, $driver, $error, $step, $parameter;

   // Prenons une copie d'écran à tout hasard....
   if (is_object($driver)) takeSnapshot();

   fwrite(STDERR, "Arrêt du script à l'étape $step. Catch\n". $exception->getMessage() . "\nCatch\n");
   if (is_object($mail)) {
      $mail->Body = $mail->Body . "<br>" . $exception->getMessage() . "<br>" ;
      $mail->Subject = $mail->Subject . " Etape $step";
      $mail->send();
   }
   return 1;
}

// Exceptions "normales"
function exception_normale($exception)
{
   global $scenario, $mail, $driver, $error, $step, $parameter;

   // Prenons une copie d'écran à tout hasard....
   if (is_object($driver)) takeSnapshot();

   fwrite(STDERR, "Normale\n". $exception->getMessage() . "\nNormale\n");
   if (is_object($mail)) {
      $mail->Body = $mail->Body . "<br>" . $exception->getMessage() . "<br>" ;
      $mail->Subject = $mail->Subject . " Etape $step";
   }
   $error += 1;
   return $error;
}

$error = 0;
//set_exception_handler('exception_handler');

///////////////////////////////////////////////////////////////////
//  Sortie propre                                                //
///////////////////////////////////////////////////////////////////

function fin($exit_code=0, $message='fin de simulation')
{
   global $driver, $mail, $error;

   addBody("$message<br>");
   $mail->Subject = "Sortie normale code $exit_code, message $message";
   echo "$message\n";

   // Si le script a échoué 
   if ($error > 0 || $exit_code > 0) {
      $exit_code = max($error, $exit_code);
      $mail->send();
   }

   // Ferme le navigateur
   if (is_object($driver))
   {
	   $driver->close();
	   $driver->quit();
   }	  
   exit($exit_code);

}

///////////////////////////////////////////////////////////////////
// Calcul du temps d'execution de chaque étape                   //
///////////////////////////////////////////////////////////////////
function logTime()
{
   global $timeLast;

   $timeCurrent = round(microtime(true) * 1000);
   $elapsed = $timeCurrent - $timeLast;
   $timeLast = $timeCurrent;
   return $elapsed;
}


///////////////////////////////////////////////////////////////////
// Gestion des mails                                             //
///////////////////////////////////////////////////////////////////
function initialiseMail()
{
   global $mail;
   if (isset($mail)) unset($mail);                       // Start fresh 
   $mail = new PHPMailer(true);                          // Passing `true` enables exceptions

   //Server settings
   $mail->SMTPDebug = 0;                                 // Enable verbose debug output
   $mail->isSMTP();                                      // Set mailer to use SMTP
   $mail->Host = 'smtp.saintmaur.local';                 // Specify main and backup SMTP servers
   $mail->SMTPAuth = false;                              // Disable SMTP authentication

   //Recipients
   $mail->setFrom('Supervision_Applicative@mairie-saint-maur.com', 'Supervision Applicative');
#   $mail->addAddress('blaise.thauvin@mairie-saint-maur.com', 'Blaise Thauvin');     // Add a recipient
   $mail->addAddress('hugo.stephan@mairie-saint-maur.com', 'Hugo STEPHAN');     // Add a recipient
#   $mail->addReplyTo('blaise.thauvin@mairie-saint-maur.com', 'Blaise Thauvin');
   $mail->addReplyTo('hugo.stephan@mairie-saint-maur.com', 'Hugo STEPHAN');

   //Content
   //$mail->Charset('UTF-8');
   $mail->setLanguage('fr', '/opt/AppMonitor/vendor/phpmailer/phpmailer/language/');
   $mail->isHTML(true);                                  // Set email format to HTML
   $mail->Subject = 'Echec scenario';
   $mail->Body    = '';
}

function addBody($text)
{
   global $mail;
   if(!isset($mail)) { $mail = new StdClass(); }
   $mail->Body = $mail->Body . $text;
}


///////////////////////////////////////////////////////////////////
// Prend un snapshot de l'état courant du test en indiquant l'heure et l'étape
///////////////////////////////////////////////////////////////////
function takeSnapshot()
{
   global $mail, $driver, $error, $step, $parameter;

   if (is_object($driver)) {
      $screenshot = SCREENSHOT_DIR."screenshot-$parameter-$step-". date("Y-m-d_H-i-s") . ".png";
      try {
         $driver->takeScreenshot($screenshot);
      }
      catch(Exception $e) {
         fwrite(STDERR, "Impossible de prendre une copie d'écran\n");
         $mail->Body = $mail->Body . "<br>Impossible de prendre une copie d'écran.<br>" . $e->getMessage() . "<br>" ;
         $error += 1;
         return $error;
      }
      $mail->addAttachment($screenshot);
   }
   return 0;
}


///////////////////////////////////////////////////////////////////
// Initialise et paramètre le navigateur pour la simulation      //
///////////////////////////////////////////////////////////////////
function initialiseDriver($timeout)
{
	global $driver ;
	// Execution du navigateur sur le serveur défini par la conf
	$host = SELENIUM_HOST;

	// Initialisation du mail d'erreur
	initialiseMail();


	// Choix du navigateur
	$options = new ChromeOptions();
	$options->addArguments(array("--start-maximized"));
	$capabilities = DesiredCapabilities::chrome();
	$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

	$driver = null ;

	// Lancement du navigateur sur le client cible, timeout de 10 secondes
	try
	{
	   $driver = RemoteWebDriver::create($host, $capabilities, $timeout, $timeout);
	} 
	catch(Exception $e) {
	   global $error;
	   fwrite(STDERR, "Première tentative de lancement du navigateur échouée\n");
	   fwrite(STDERR, "$e->getMessage()");
	   $mail->Subject = "Première tentative de lancement du navigateur échouée";
	   addBody("$e->getMessage()");
	}   

	// Si on n'a pas réussi à lancer le navigateur, on essaye encore une fois
	if (!is_object($driver)) {
	   try {
		  $driver = RemoteWebDriver::create($host, $capabilities, $timeout, $timeout);
	   }
	   catch(Exception $e) {
		  global $error;
		  fwrite(STDERR, "Deuxième tentative de lancement du navigateur échouée\n");
		  fwrite(STDERR, "$e->getMessage()");
		  $mail->Subject = "Deuxième tentative de lancement du navigateur échouée";
		  addBody("$e->getMessage()");
		  $mail->Subject = "Impossible de lancer le navigateur";
		  $error += 1;
		}
	}

	// A t on réussi à lancer le navigateur? Si non on arrête le script ici
	if (!is_object($driver)) fin(1, "\nTests Selenium KO, lancement navigateur impossible\n");
}

function closeDriver()
{
	global $driver;
	
	$driver->close();
	$driver->quit();
}

///////////////////////////////////////////////////////////////////
// Paramètres du navigateur cible pour la simulation             //
///////////////////////////////////////////////////////////////////

// Execution du navigateur sur le serveur défini par la conf
$host = SELENIUM_HOST;

// Initialisation du mail d'erreur
$driver = null ;
initialiseMail();


///////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////
$nsca_status = EonNsca::STATE_UNKNOWN;
$nsca_msg = "Selenium Web Test : UNKNOWN STATE";
// Boucle sur les arguments passés en ligne de commande
foreach ($argv as $key => $parameter) {
   if ($key == 0) continue; 

   $error = 0;
   initialiseMail();
   initialiseDriver(8000);
   addBody("<br>$parameter<br>");
   echo "\nScenario $parameter\n-----------------\n"; 
   $mail->Subject = "ECHEC Scenario $parameter";

   // Instanciation de la classe de scénario
   require_once("testcases/$parameter.php");
   $scenario = new $parameter($driver);

   // Instanciation de la classe permettant le stockage des données en base circulaire
   $RRD = new ReportingTool($parameter);

   if ($error == 0) {
      $step = 'Home';
      logTime();
      try {
         $scenario->goHome();
      } 
      catch(Exception $exception) {
         fwrite(STDERR, "Normale\n". $exception->getMessage() . "\nNormale\n");
         exception_normale($exception);
      }
      if ($error == 0) $RRD->timeHome = logTime();
      takeSnapshot();
   }

   if ($error == 0) {
      $step = 'Login';
      logTime();
      try {
         $scenario->Login();
      } 
      catch(Exception $exception) {
         fwrite(STDERR, "Normale\n". $exception->getMessage() . "\nNormale\n");
         exception_normale($exception);
      }
      if ($error == 0) $RRD->timeLogin = logTime();
      takeSnapshot();
   }

   if ($error == 0) {   
      $step = 'Actions';   
      logTime();
      try {
         $scenario->Action();
      } 
      catch(Exception $exception) {
         fwrite(STDERR, "Normale\n". $exception->getMessage() . "\nNormale\n");
         exception_normale($exception);
      }
      if ($error == 0) $RRD->timeActions = logTime();
      takeSnapshot();
   }
   if ($error == 0) {
      $step = 'Logout';
      logTime();
      try {
         $scenario->Logout();
      } 
      catch(Exception $exception) {
         fwrite(STDERR, "Normale\n". $exception->getMessage() . "\nNormale\n");
         exception_normale($exception);
      }
      if ($error == 0) $RRD->timeLogout = logTime();
      takeSnapshot();
   }

   // Suppression des éventuels cookies résiduels
   if (is_object($driver)) {
      try {
         $driver->manage()->deleteAllCookies();
      }
      catch(Exception $e) {
         addBody("Aucun cookie à supprimer<br>");
         echo "Aucun cookie à supprimer\n";
      }
   }
   // Enregistrement des données par destruction de la classe RRD
   addBody("Home:    $RRD->timeHome ms<br>");
   addBody("Login:   $RRD->timeLogin ms<br>");
   addBody("Actions: $RRD->timeActions ms<br>");
   addBody("Logout:  $RRD->timeLogout ms<br>");
   addBody("Total:   " . ($RRD->timeHome + $RRD->timeLogin + $RRD->timeActions + $RRD->timeLogout) . " ms<br>");
   if ( $RRD->timeLogout == 'U' ) $error+=1;


   // Afficher le titre de la page courante
   if (is_object($driver) && !empty($driver->getTitle())) addBody("Le titre de la dernière page est: " . $driver->getTitle() . "<br>");

   // Afficher l'URL de la page actuelle
   if (is_object($driver) && !empty($driver->getCurrentURL())) addBody("L'URL finale est: " . $driver->getCurrentURL() . "<br>");

   echo "Niveau d'erreur = $error\n";


  
   addBody("Scenario $parameter OK<br>------------------<br>");
   if ($error > 0 ) {
      try {
         $mail->send();
         echo "Message has been sent\n";
      } 
      catch(Exception $e) {
         fwrite(STDERR, "Message could not be sent. Mailer Error:\n". $mail->ErrorInfo . "\n");
      }
	  $nsca_status = EonNsca::STATE_CRITICAL ;
	  $nsca_msg = "Selenium Web Test : FAILURE" ;
   }
   else
   {
	   $nsca_status = EonNsca::STATE_OK ;
	   $nsca_msg = "Selenium Web Test : SUCCESS" ;
   }

   array_map('unlink', glob("screenshot-$parameter-*.png"));
   $RRD->nsca_report($nsca_status, $nsca_msg);
   
   
   unset($RRD);
   // Destruction de la classe de scénario
   unset($scenario);
   unset($mail);
   
   closeDriver($driver);
}

// Fermeture du navigateur et sortie
fin(0, "\nTests Selenium OK\n");

?>
