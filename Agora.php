<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//     Script de test applicatif Agora pour Selenium 2          //
//                                                              //
//                   Blaise 20-01-2018   V0.1                   //
//                                                              //
//////////////////////////////////////////////////////////////////

class Agora extends TestSelenium {

   // Test applicatif 
   public function doScenario() {
      global $driver, $RRD, $timeStart;

      // Ouverture de la page d'accueil de l'application
      $driver->get('http://10.51.0.8/agora/pck_security.home');
      $timeCurrent = round(microtime(true) * 1000);
      $RRD->timeHome = $timeCurrent - $timeStart;
      $timeLast = $timeCurrent;

      // Suppression des éventuels cookies résiduels
      $driver->manage()->deleteAllCookies();


      // On cherche les 3 boutons colorés des 3 domaines d'Agora
      // puis on clique aveuglément sur le deuxième  
      $elements = $driver->findElements(WebDriverBy::className('dock-item'));
      $nbElements = count($elements);
      if ($nbElements <> 3) 
         fin("On attendait 3 disques clicables, on en a obtenu $nbElements.\n");
      else
         $elements[1]->click();

      // On attend l'affichage du bloc de login
      $element = $driver->wait()->until(Facebook\WebDriver\WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::name('p_login')));

      // Saisie du login et du mot de passe puis validation
      $driver->findElement(WebDriverBy::name('p_login'))->sendKeys('Tdsi');
      $driver->findElement(WebDriverBy::name('p_pass'))->sendKeys('DSI94100');
      $link = $driver->findElement(WebDriverBy::id('logIn'));
      $link->click();

      // On attend l'affichage effectif de la première page
      $driver->wait()->until(WebDriverExpectedCondition::titleContains('Agor@Baby'));
      $timeCurrent = round(microtime(true) * 1000);
      $RRD->timeLogin = $timeCurrent - $timeLast;
      $timeLast = $timeCurrent;

      // Un cookie a peut-être été postionné, on l'affiche
      //$cookies = $driver->manage()->getCookies();
      //print_r($cookies);


      // Clic sur les items de menu
      $link = $driver->findElement(WebDriverBy::linkText('GESTION DE LA POPULATION'));
      $link->click();

      $link = $driver->findElement(WebDriverBy::linkText('FAMILLES'));
      $link->click();

      $link = $driver->findElement(WebDriverBy::linkText('Rechercher'));
      $link->click();
      $timeCurrent = round(microtime(true) * 1000);
      $RRD->timeActions = $timeCurrent - $timeLast;
      $timeLast = $timeCurrent;

      $link = $driver->findElement(WebDriverBy::linkText('DÉCONNEXION'));
      $link->click();
      $timeCurrent = round(microtime(true) * 1000);
      $RRD->timeLogout = $timeCurrent - $timeLast;
      $timeLast = $timeCurrent;
   }
} 
