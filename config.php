<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Fichier de configuration AppMonitor                      //
//                                                              //
//                STEPHAN Hugo  26-03-2018   V0.1               //
//                LEJARRE Camus 11-05-2018   V0.2               //
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
	// Dossier pour les caputres
			$SCREENSHOT_DIR = "./screenshots/",
	// Host selenium
			$SELENIUM_HOST = "http://srv-eon.saintmaur.local:4444/wd/hub",
			$SELENIUM_HOST_NAME = "SRV-EON",
	//Chrome settings
			$CHROME_OPTIONS = ["--start-maximized","--headless","--no-default-browser-check","--incognito", "--window-size=1920,1080"],
	//RRDTool
			$RRD_TOOL = "/opt/rrdtool-1.7.0/bin/rrdtool",
			$RRD_UPD = "/opt/rrdtool-1.7.0/bin/rrdupdate",
			$RRD_DEFAULT_FILE = "default.rrd",
	// SSH
			$SSH_HOST_STATUS_FILES = "www01-d.saintmaur.local",
			$SSH_HOST1 = "www03-p.dmz.saintmaur.local",
			$SSH_HOST2 = "www04-p.dmz.saintmaur.local",
			$SSH_PORT = 22,
			$SSH_FP = "19:f8:27:1f:df:64:02:d4:38:5d:83:16:f7:dc:91:cf",
			$SSH_AUTH_USER = 'root',
			$SSH_AUTH_PUB = '/root/.ssh/id_rsa.pub',
			$SSH_AUTH_PRIV = '/root/.ssh/id_rsa',
			$NAGIOS_DAT_FILE_DIR = "/srv/eyesofnetwork/nagios/var/log/", //dossier où trouver le fichier DAT de Nagios
			$STATUS_FILE_DIR = "/var/www/html/listapp/app_status/",//dossier où stocker les fichiers .status
	// TIMEOUT DELAYS (in Miliseconds)
			$CONNECT_TIMEOUT = 15000,
			$QUERY_TIMEOUT = 120000,
	// EON NSCA
			$EON_SRV = 'localhost';
	
	public function getChromeDirName(){
		return "--user-data-dir=/tmp/chromedata".date("_Ymd_h-i-s");
	}
}
//////////////////////////////////////////////////////////////////
//                           INCLUDE                            //
//////////////////////////////////////////////////////////////////

require_once('vendor/phpmailer/phpmailer/class.phpmailer.php');
require_once('vendor/phpmailer/phpmailer/class.smtp.php');
require_once('DriverWrapper.php');
require_once('NiceMail.php');
require_once('ReportingTool.php');
require_once('testcases/Scenario.php');
require_once('Exception.php');

?>
