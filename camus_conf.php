<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Fichier de configuration AppMonitor                      //
//                                                              //
//                LEJARRE Camus 10-04-2018   V0.2               //
//                                                              //
//////////////////////////////////////////////////////////////////

define("SCREENSHOT_DIR", "./screenshots/");

define("SELENIUM_HOST", "http://sm00597.saintmaur.local:4444/wd/hub"); 

define("RRD_TOOL", "/opt/rrdtool-1.7.0/bin/rrdtool");

define("RRD_UPD", "/opt/rrdtool-1.7.0/bin/rrdupdate");

define("RRD_DEFAULT_FILE", "default.rrd");

define("SSH_HOST", "www01-d.saintmaur.local");

define("SSH_PORT", 22);

define("SSH_FP", "19:f8:27:1f:df:64:02:d4:38:5d:83:16:f7:dc:91:cf");

define("SSH_AUTH_USER", 'root') ;

define("SSH_AUTH_PUB", '/root/.ssh/id_rsa.pub') ;

define("SSH_AUTH_PRIV", '/root/.ssh/id_rsa');

define("CONNECT_TIMEOUT", 7000);
define("QUERY_TIMEOUT", 60000);

require_once('vendor/phpmailer/phpmailer/class.phpmailer.php');
require_once('vendor/phpmailer/phpmailer/class.smtp.php');
require_once('ReportingTool.php');
require_once('testcases/Scenario.php');

?>

