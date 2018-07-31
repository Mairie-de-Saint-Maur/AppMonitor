#!/usr/bin/php
<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif pour Selenium 2                //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////

/* GET OPT : 
	-s (obligatoire) pour le nom du scenario;
	-c (facultatif) pour le fichier de config à utiliser;
	-i (facultatif)pour l'intervalle horaire permis pour les mails;
	-v (facultatif)pour le mode verbose;
	-h ou -help (facultatif) liste les paramètres;
	
 * retourne un array avec :
 * $cl_opt[s] = nom du scenario
 * $cl_opt[c] = fichier de config à utiliser ou FALSE si rien n'a été saisi
 * $cl_opt[i] = intervalle horaire pendant lequel le script va envoyer des notifs par emial, format 8-22 != 22-8
 * $cl_opt[v] = mode verbose affichant le détail des étapes en console
 */
$cl_opt = getopt("s:c::i::v::h::-help::");

//Mode Verbose
$verbose = (isset($cl_opt['v']))? true : false ;
if($verbose) echo "Mode verbose \e[1;33mactivé\e[0m\n\n";

Console("Récupération des paramètres de ligne de commande\n");
//Si les options génèrent une erreur, on stoppe le script
if(!$cl_opt or (!isset($cl_opt['s']) and !isset($cl_opt['h']) and !isset($cl_opt['help']))){
	echo "\n\e[0;31m /!\ ERREUR\e[0m : Les options passées sont non conformes. Utilisez -h ou -help pour l'aide.\n\n";
	exit;
}elseif (isset($cl_opt['h']) or isset($cl_opt['help'])){
	echo "\n\e[0;32mAIDE pour TestSelenium\e[0m
Les paramètres disponibles sont :
	\e[1;33m-s\e[0m (\e[0;31mobligatoire\e[0m) pour le nom du scenario
	\e[1;33m-c\e[0m (facultatif) pour le fichier de config à utiliser (config.php est utilisé si le paramètre est omis)
	\e[1;33m-i\e[0m (facultatif)pour l'intervalle horaire permis pour les mails (0-24 est utilisé si le paramètre est omis)
	\e[1;33m-v\e[0m (facultatif)pour le mode verbose (qui affiche le détail d'avancement du scénario)
	\e[1;33m-h\e[0m ou \e[1;33m-help\e[0m (facultatif) liste les paramètres
";
	exit;
}else{
	
	Console("Chargement des bibliothèques \e[1;33mFB-Webdriver\e[0m (autoload Composer)\n");
	require_once('vendor/autoload.php');
	Console("[\e[0;32mOK\e[0m]\n\n");

	//Vérification de la présence d'un fichier config.php par défaut
	if(!file_exists("config.php")){
		Console("Fichier de configuration \e[1;33mconfig.php \e[0;31mNON TROUVÉ.\nImpossible de continuer.\e[0m.\n");
		exit;
	}
	//Recup fichier de config ou appeler la valeur par défaut
	$conf = (isset($cl_opt['c']))? $cl_opt['c'] : 'config.php';
	
	//Recup de l'intervalle passé en paramétre ou utilisation de celui par défaut: 24/24h
	$interval = (isset($cl_opt['i']))? $cl_opt['i'] : '7-22';

	//on vérifie si le fichier demandé existe ou on impose le fichier config.php
	if(!file_exists($conf)){
		Console("Fichier de configuration \e[1;33m$conf \e[0;31mNON TROUVÉ\e[0m, utilisation de \e[1;33mconfig.php\e[0m à la place\n");
		$conf = 'config.php';
	}
	$scenario_n = $cl_opt['s'];
}
Console("[\e[0;32mOK\e[0m]\n\n");

//Fichier de configuration
Console("Chargement du fichier de configuration \e[1;33m$conf\e[0m\n");
require_once($conf);
Console("[\e[0;32mOK\e[0m]\n\n");

//Vérification de la présence des dossiers
Console("\e[0;32mVérification\e[0m de la présence des dossiers :\n");
Config::CheckFolders(Config::$CREATE_DIR_IF_ABSENT); //CheckFolders prend en paramètre TRUE (par défaut) pour créer les dossiers s'ils n'existent pas, ou FALSE pour ne pas les créer.
Console("[\e[0;32mOK\e[0m]\n\n");

//Création du mail à partir de la classe NiceMail
Console("Création de l'objet \e[1;33mNiceMail\e[0m\n");

//Config des adresses de dest et reply depuis le fichier de conf
$params['dest'] = (isset($dest))? $dest : null;
$params['reply'] = (isset($reply))? $reply : null;
$params['exceptions'] = true;
$mail = new NiceMail($params);

//Ajout du nom du scénario dans le corps du mail
$mail->Subject = "ECHEC Scenario $scenario_n";
if(Config::$SELENIUM_HOST_NAME) $mail->Subject =  $mail->Subject." sur ". Config::$SELENIUM_HOST_NAME;
$mail->addBody("<h1>Scénario $scenario_n</h1><p>Exécuté depuis ". Config::$SELENIUM_HOST."</p>");
Console("[\e[0;32mOK\e[0m]\n\n");

//Niveau d'erreur et gestionnaire d'exceptions
set_exception_handler('exception_handler');

///////////////////////////////////////////////////////////////////
// Test applicatif                                               //
///////////////////////////////////////////////////////////////////

$nsca_status = EonNsca::STATE_UNKNOWN;
$nsca_msg = "Selenium Web Test : UNKNOWN STATE";

//Création du driver
$driver_w = new DriverWrapper($mail);
$driver = $driver_w->getDriver();

//On instancie le scénario appelé
$scenario = Scenario::createScenario($driver, $scenario_n);

//On vérifie si une autre instance du scénario n'est pas déjà en cours d'execution et non finie
if($scenario->isLocked()){
	Console("\e[1;37mLe scenario précédent n'est pas terminé\e[0m\n[\e[0;31mSKIP\e[0m]\n\n");
	exit;
}else{
	Console("Vérification que le scénario précédent est \e[1;33mterminé\n\e[0m[\e[0;32mOK\e[0m]\n\n");
	$scenario->lock();
}
// Instanciation de la classe permettant le stockage des données en base circulaire
$RRD = new ReportingTool($scenario, $driver);
//On utilise les mêmes étapes pour le RRD que celle que le scénario a prévu.
$RRD->setSteps($scenario->getSteps());

//Vérification de communication avec Chrome
try {
	$driver->get(Config::$TEST_COMM_URL);
}
catch(Exception $exception) {
	//En cas d'erreur :
	$driver_w->setError(exception_at_start($exception, $driver_w, $scenario));
}

//Titre coloré : permet de mieux repérer le début de l'exécution du script #USER_FRIENDLY
Console("\e[1;37mDemarrage du scenario \e[1;34m$scenario_n\n\e[0m\n");

//On parcourt les étapes prévues au scénarion, par défaut : GoHome, Login, Action, Logout.
foreach($scenario->getSteps() as $step){
	//On vérifie que l'étape demandée existe et on init les variables
	$scenario->init_step($step);
	if ($driver_w->getError() == 0) {
		//init du counter
		$driver_w->logTime();
		//exécution de l'étape
		try {
			$scenario->$step();
			//temps d'exécution de l'étape
			Console("...".$driver_w->logTime($step)."ms\n");
		} 
		catch(Exception $exception) {
			//En cas d'erreur :
			$driver_w->setError(exception_normale($exception, $driver_w, $scenario, $driver_w->getError()));
		}
		
		//Enregistrement RRD
		if ($driver_w->getError() == 0){
			Console("Enregistrement RRD (".$driver_w->getTimes($step)."ms)");
			$RRD->setTime($step, $driver_w->getTimes($step));
			Console("[\e[0;32mOK\e[0m]\n");
		}
		
		//Capture d'écran
		Console("Capture d'écran ");
		$driver_w->takeSnapshot($scenario->getStep(),$scenario->getName());
		Console("[\e[0;32mOK\e[0m]\n");
	}
	Console("Fin d'étape : $step\n\n");
}

Console("\e[1;37mFin du scenario \e[1;34m$scenario_n\n\e[0m\n");

// Suppression des éventuels cookies résiduels
if (is_object($driver)) {
  try {
	 $driver->manage()->deleteAllCookies();
  }
  catch(Exception $e) {
	 $mail->addBody("<div class='info'>Aucun cookie à supprimer</div>");
	 Console("\e[0;31m /!\ ERREUR\e[0m : Aucun cookie à supprimer\n\n");
  }
}

$total = 0;
$mail->addBody("<table class='times_table'><tr><th>Etape</th><th>Temps</th></tr>");
foreach ($RRD->getTimes() as $step=>$time){
	if($time != 'U'){
		$total += $time;
		$time .= " ms";
	}else{
		$time = "Timeout";
	}
	$mail->addBody("<tr><td>$step</td><td class='align_right'>$time</td></tr>");
}

$mail->addBody("<tr><td class='bold'>Total:</td><td>$total ms</td></tr></table>");

if ( end($RRD->getTimes()) == 'U' ) $driver_w->addError(4);

// Afficher le titre de la page courante
//if (is_object($driver) && $driver->getTitle() != null) $mail->addBody("Le titre de la dernière page est: " . $driver->getTitle() . "<br>");

// Afficher l'URL de la page actuelle
//if (is_object($driver) && $driver->getCurrentURL() != null) $mail->addBody("L'URL finale est: " . $driver->getCurrentURL() . "<br>");

//echo "\n\e[1;34mNiveau d'erreur\e[0m : $driver_w->getError()\n\n";

//$mail->addBody("Scenario $scenario_n OK<br>------------------<br>");
if ($driver_w->getError() > 0 )
{
	try {
		// On envoie la notif que si on est dans le bon intervalle
		$d = idate('H');
		$min = explode('-', $interval)[0];
		$max = explode('-', $interval)[1];
		
		// l'intervalle peut être dans 2 'sens' contraire 8-22 != 22-8 
		if ( ($min < $max && $d > $min && $d < $max) || ($min > $max && ($d < $min || $d > $max) ) )
		{
			$mail->send();
		}
		Console("\e[1;34mINFO\e[0m : Mail sent !\n\n");
	} 
	catch(Exception $e) {
		fwrite(STDERR, "Mail could not be sent. Mailer Error:\n". $mail->ErrorInfo . "\n");
	}
	$nsca_status = EonNsca::STATE_CRITICAL ;
	$nsca_msg = "\e[0;31mFAILURE\e[0m" ;
}
else
{
	$nsca_status = EonNsca::STATE_OK ;
	$nsca_msg = "\e[0;32mSUCCESS\e[0m" ;
}

//Suppression des fichiers images
array_map('unlink', glob( Config::$SCREENSHOT_DIR."screenshot-$scenario_n-*.png"));

//NSCA Report
$RRD->nsca_report($nsca_status, $nsca_msg);

// Enregistrement des données par destruction de la classe RRD
unset($RRD);
// Destruction de la classe de scénario
unset($scenario);
//Destruction de la class NiceMail
unset($mail);

// Fermeture du navigateur et sortie
$driver_w->fin(0, "Fin des tests Selenium");

function Console($text){
	global $verbose;
	if($verbose) echo($text);
}
?>
