<?php
//namespace Facebook\WebDriver;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Agora.php;
use TestSelenium.php;

require_once('vendor/autoload.php');

$Case = new Agora():
echo 1;
$Case->doScenario();
echo 2;
$Case->Summary();
$Case->fin(0, "Agora OK");

?>
