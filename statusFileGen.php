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

//Config
require_once('camus_conf.php');

//NiceSSH class
require_once('NiceSsh.php');

// Change this accordingly
$statusFile = Config::$NAGIOS_DAT_FILE_DIR."status.dat"; 
$statusFilesDir = Config::$STATUS_FILE_DIR;

global $debug;

	if(!file_exists($statusFile)){
		echo "Le fichier DAT de Nagios n'a pas été trouvé.";
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
                    echo "LINE " . $lineNum . ": lineKey=" . $lineKey . "= lineVal=" . $lineVal . "=\n";
                }
				//Nettoyage du statut
                $sectionData[$lineKey] = preg_replace('/\[[0-9:]*[m]/m','',$lineVal);
            }
            // else continue on, ignore this section, don't save anything
        }

    }

    fclose($fh);
	
	$ssh = new NiceSsh();
	$ssh->connect(Config::$SSH_HOST_STATUS_FILES);
	
	//Pour chaque appli, on génère le fichier status correspondant
	foreach($serviceStatus as $key => $app)
	{
		$cmd = 'echo '.$app['plugin_output'].' > '.$statusFilesDir.$key.'.status';
		$ssh->exec($cmd);
	}
?>