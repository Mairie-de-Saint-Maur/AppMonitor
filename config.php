<?php

require_once('vendor/phpmailer/phpmailer/class.phpmailer.php');
require_once('vendor/phpmailer/phpmailer/class.smtp.php');
require_once('ReportingTool.php');
require_once('testcases/Scenario.php');

$SCREENSHOT_DIR = "";

$SELENIUM_HOST = "http://test01-x.saintmaur.local:4444/wd/hub"; 

$RRD_TOOL = "/opt/rrdtool-1.7.0/bin/rrdtool";

$RRD_UPD = "/opt/rrdtool-1.7.0/bin/rrdupdate";

$RRD_DEFAULT_FILE = "default.rrd";

$SSH_HOST = "www01-d.saintmaur.local";

$SSH_PORT = 22;

$SSH_FP = "19:f8:27:1f:df:64:02:d4:38:5d:83:16:f7:dc:91:cf";

$SSH_AUTH_USER = 'root' ;

$SSH_AUTH_PUB = '/root/.ssh/id_rsa.pub' ;

$SSH_AUTH_PRIV = '/root/.ssh/id_rsa';

?>