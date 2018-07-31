<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//               Fichier de configuration AppMonit              //
//                                                              //
//                LEJARRE Camus 10-04-2018   V0.2               //
//                                                              //
//////////////////////////////////////////////////////////////////


class Config{
	static 	
	// MAIL
			$DEFAULT_DEST = array(['blaise.thauvin@mairie-saint-maur.com', 'Blaise THAUVIN'],['hugo.stephan@mairie-saint-maur.com', 'Hugo STEPHAN'],['camus.lejarre@mairie-saint-maur.com', 'Camus LEJARRE']),
			$DEFAULT_REPLY = array(['blaise.thauvin@mairie-saint-maur.com', 'Blaise THAUVIN'],['hugo.stephan@mairie-saint-maur.com', 'Hugo STEPHAN'],['camus.lejarre@mairie-saint-maur.com', 'Camus LEJARRE']),
			$DEFAULT_EXP_MAIL = 'Supervision_Applicative@mairie-saint-maur.com',
			$DEFAULT_EXP_NAME = 'Supervision Applicative',
			$SMTP = 'smtp.saintmaur.local',
	// Création des dossiers si absents
			$CREATE_DIR_IF_ABSENT = true,
	// Dossier pour les captures
			$SCREENSHOT_DIR = "./screenshots/",
	// URL de test de communication - doit renvoyer vers une URL dispo sur le réseau local pour être effective
			$TEST_COMM_URL = 'http://srv-eon.saintmaur.local',
	// Host selenium
			$SELENIUM_HOST = "http://sm00597.saintmaur.local:4444/wd/hub",
			$SELENIUM_HOST_NAME = "KMU - sm00597",
	//Chrome settings
			$CHROME_OPTIONS = ["--start-maximized","--no-default-browser-check","--incognito", "--window-size=1050,889"],
	//RRDTool
			$RRD_TOOL = "/opt/rrdtool-1.7.0/bin/rrdtool",
			$RRD_UPD = "/opt/rrdtool-1.7.0/bin/rrdupdate",
			$RRD_DEFAULT_FILE = "default.rrd",
	// SSH
			//$SSH_HOST_STATUS_FILES = "www01-d.saintmaur.local",
			$SSH_HOSTS = array("www01-d.saintmaur.local"),
			$SSH_PORT = 22,
			$SSH_FP = "19:f8:27:1f:df:64:02:d4:38:5d:83:16:f7:dc:91:cf",
			$SSH_AUTH_USER = 'root',
			$SSH_AUTH_PUB = '/root/.ssh/id_rsa.pub',
			$SSH_AUTH_PRIV = '/root/.ssh/id_rsa',
			$NAGIOS_DAT_FILE_DIR = "./", //dossier où trouver le fichier DAT de Nagios
			$NAGIOS_HOST_NAME = 'Applications', //nom du host Nagios dans lequel se trouvent les services
			$STATUS_FILE_DIR = "/var/www/html/dev/listapp/app_status/",//dossier où stocker les fichiers .status
	// TIMEOUT DELAYS (in Miliseconds)
			$CONNECT_TIMEOUT = 15000,
			$QUERY_TIMEOUT = 120000,
	// Emplacement des fichiers LOCK
			$LOCKFILE_FOLDER = './lockfiles/',
			$LOCKFILE_MIN_EXPIRE = 60*3, //3 minutes
	// EON NSCA
			$EON_SRV = 'srv-eon.saintmaur.local';
			
	public function getChromeDirName(){
		return "--user-data-dir=/tmp/chromedata".date("_Ymd_h-i-s");
	}
	
	public function CheckFolders($create = true){
		//Screenshots
		Console("1- \e[1;33m".Config::$SCREENSHOT_DIR."\e[0m\n");
		if (!is_dir(Config::$SCREENSHOT_DIR) or !file_exists(Config::$SCREENSHOT_DIR)){
			Console("   [\e[0;31mNOT FOUND\e[0m]\n");
			if ($create){
				Console("   Création du dossier \e[1;33m".Config::$SCREENSHOT_DIR."\e[0m\n");
				if(mkdir(Config::$SCREENSHOT_DIR, 0777)){
					Console("   [\e[0;32mOK\e[0m]\n\n");
				}else{
					Console("   [\e[0;32mImpossible de créer le dossier.\e[0m]\n\n");
					exit;
				}
			}else{
				Console("   [\e[0;31mTest arrêté\e[0m] : les dossiers requis ne sont pas présents.\n\n");
				exit;
			}
		}else{
			Console("   [\e[0;32mOK\e[0m]\n\n");
		}
		
		//Lockfiles
		Console("2- \e[1;33m".Config::$LOCKFILE_FOLDER."\e[0m\n");
		if (!is_dir(Config::$LOCKFILE_FOLDER) or !file_exists(Config::$LOCKFILE_FOLDER)){
			Console("   [\e[0;31mNOT FOUND\e[0m]\n");
			if ($create){
				Console("   Création du dossier \e[1;33m".Config::$LOCKFILE_FOLDER."\e[0m\n");
				if(mkdir(Config::$LOCKFILE_FOLDER)){
					Console("   [\e[0;32mOK\e[0m]\n\n");
				}else{
					Console("   [\e[0;32mImpossible de créer le dossier.\e[0m]\n\n");
					exit;
				}
			}else{
				Console("   [\e[0;31mTest arrêté\e[0m] : les dossiers requis ne sont pas présents.\n\n");
				exit;
			}
		}else{
			Console("   [\e[0;32mOK\e[0m]\n\n");
		}
	}
}
	$dest = array(['camus.lejarre@mairie-saint-maur.com', 'Camus LEJARRE']);
	$reply = array(['camus.lejarre@mairie-saint-maur.com', 'Camus LEJARRE']);
	
//////////////////////////////////////////////////////////////////
//                           INCLUDE                            //
//////////////////////////////////////////////////////////////////

require_once('vendor/phpmailer/phpmailer/class.phpmailer.php');
require_once('vendor/phpmailer/phpmailer/class.smtp.php');
require_once('DriverWrapper.php');
require_once('NiceMail.php');
require_once('ReportingTool.php');
require_once('Scenario.php');
require_once('Exception.php');
?>