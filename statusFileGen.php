<?php
// statusFileGen.php
//
// Script to parse Nagios status.dat and push .status file for each service
// in "Applications"
//
// This code is just a modification of Jason Antman's statusXML.php available at
// https://github.com/jantman/php-nagios-xml
//
// +----------------------------------------------------------------------+
// | Copyright (c) 2013 Christian Lizell.                                 |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// +----------------------------------------------------------------------+

//Composer
require_once('vendor/autoload.php');

$cl_opt = getopt("c::");
//Recup fichier de config ou appeler la valeur par défaut
$conf = (isset($cl_opt['c']))? $cl_opt['c'] : 'config.php';
//Si c'est via la page web :
$conf = (isset($_GET['c']))? $_GET['c'] : 'config.php';
//on vérifie si le fichier demandé existe ou on impose le fichier config.php
if(!file_exists($conf)){
	Console("Fichier de configuration \e[1;33m$conf \e[0;31mNON TROUVÉ\e[0m, utilisation de \e[1;33mconfig.php\e[0m à la place\n");
	$conf = 'config.php';
	if(!file_exists($conf)){
		Console("Fichier de configuration \e[1;33m par défaut \e[0;31mNON TROUVÉ\e[0m, fin du script\n");
		exit;
	}
}

//Config
require_once($conf);
//NiceSSH class
//require_once('NiceSsh.php');

// Change this accordingly
$statusFile = Config::$NAGIOS_DAT_FILE_DIR."status.dat"; 
$statusFilesDir = Config::$STATUS_FILE_DIR;

global $debug;

	if(!file_exists($statusFile)){
		echo "Le fichier DAT de Nagios ($statusFile) n'a pas été trouvé.\n";
		exit;
	}
    # open the file
    $fh = fopen($statusFile, 'r');
	
    # variables to keep state
    $inSection = false;
    $sectionType = "";
    $lineNum = 0;
    $sectionData = array();

    $hostStatus = array();
    $serviceStatus = array();
    $programStatus = array();

    #variables for total hosts and services
    $typeTotals = array();

    # loop through the file
    while ($line = fgets($fh)) {
        $lineNum++; // increment counter of line number, mainly for debugging
        $line = trim($line); // strip whitespace
        if ($line == "") {
            continue;
        } // ignore blank line
        if (substr($line, 0, 1) == "#") {
            continue;
        } // ignore comment

        // ok, now we need to deal with the sections
        if (!$inSection) {
            // we're not currently in a section, but are looking to start one
            if (substr($line, strlen($line) - 1, 1) == "{") // space and ending with {, so it's a section header
            {
                $sectionType = substr($line, 0, strpos($line, " ")); // first word on line is type
                $inSection = true;
                // we're now in a section
                $sectionData = array();

                // increment the counter for this sectionType
                if (isset($typeTotals[$sectionType])) {
                    $typeTotals[$sectionType] = $typeTotals[$sectionType] + 1;
                } else {
                    $typeTotals[$sectionType] = 1;
                }

            }
        } elseif ($inSection && trim($line) == "}") // closing a section
        {
			//On n'ajoute le service que s'il appartient à Applications
            if ($sectionType == "servicestatus" && $sectionData['host_name'] == 'Applications') {
                $serviceStatus[$sectionData['service_description']] = $sectionData;
            }
			
			
			//On regarde s'il y a un downtime pour le service
            if ($sectionType == "servicedowntime" && $sectionData['host_name'] == 'Applications') {
				$soft = $sectionData['service_description'];
				//On enlève les informations redondantes
				unset($sectionData['service_description']);
				unset($sectionData['host_name']);
				//On stocke la liste reçue des périodes de maintenance
					//$serviceStatus[$soft]['downtime'][] = $sectionData;
				//On met à jour le statut si l'heure tombe dans la fourchette
				if ($sectionData['start_time'] <= mktime() && $sectionData['end_time'] >= mktime()) $serviceStatus[$soft]['plugin_output'] = 'WARNING';
            }
			
            $inSection = false;
            $sectionType = "";
            continue;
        } else {
            // we're currently in a section, and this line is part of it
            $lineKey = substr($line, 0, strpos($line, "="));
            $lineVal = substr($line, strpos($line, "=") + 1);

            // add to the array as appropriate mais en sélectionnant les infos qu'on veut
            if ($sectionType == "servicestatus" && ($lineKey == 'host_name' || $lineKey == 'service_description' || $lineKey == 'plugin_output')) {
                if ($debug) {
                    echo "LINE " . $lineNum . ": lineKey=" . $lineKey . "= lineVal=" . $lineVal . "<br>";
                }
				//Nettoyage du statut
                $sectionData[$lineKey] = preg_replace('/\[[0-9:]*[m]/m','',$lineVal);
            }// add to the array as appropriate mais en sélectionnant les infos qu'on veut
            elseif ($sectionType == "servicedowntime" && ($lineKey == 'start_time' || $lineKey == 'end_time' || $lineKey == 'comment' || $lineKey == 'host_name' || $lineKey == 'service_description')) {
                if ($debug) {
                    echo "LINE " . $lineNum . ": lineKey=" . $lineKey . "= lineVal=" . $lineVal . "<br>";
                }
				//Nettoyage du statut
                $sectionData[$lineKey] = preg_replace('/\[[0-9:]*[m]/m','',$lineVal);
            }
            // else continue on, ignore this section, don't save anything
        }

    }

    fclose($fh);
	
	//On crée deux connexions, une pour chaque serveur
	$ssh1 = new NiceSsh();
	$ssh2 = new NiceSsh();
	
	$ssh1->connect(Config::$SSH_HOST1);
	$ssh2->connect(Config::$SSH_HOST2);
	
	//Tableau qui trace les commandes exécutées
	$cmd_trace = array();
	
	//Pour chaque appli, on génère le fichier status correspondant
	foreach($serviceStatus as $key => $app)
	{
		$cmd = 'echo '.$app['plugin_output'].' > '.$statusFilesDir.$key.'.status';
		$cmd_trace[Config::$SSH_HOST1][] = $cmd;
		$ssh1->exec($cmd);
		$cmd_trace[Config::$SSH_HOST2][] = $cmd;
		$ssh2->exec($cmd);
	}

        $ssh1->exec("chmod -R 777 ".$statusFilesDir." ; chown -R apache:apache ".$statusFilesDir);	
        $ssh2->exec("chmod -R 777 ".$statusFilesDir." ; chown -R apache:apache ".$statusFilesDir);	
	var_dump($cmd_trace);
?>
