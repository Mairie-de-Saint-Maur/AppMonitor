<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//                   Gestion des exceptions                     //
//                                                              //
//                      26-03-2018   V0.1                       //
//                                                              //
//////////////////////////////////////////////////////////////////

//Exceptions "imprévues"
function exception_handler($exception)
{
   //global $scenario, $mail, $driver, $error, $step, $parameter;

   // Prenons une copie d'écran à tout hasard....
   //if (is_object($driver))  $driver->takeSnapshot();

   fwrite(STDERR, "Arrêt du script à l'étape $step. Catch\n". $exception->getMessage() . "\nCatch\n");
   if (is_object($mail)) {
      $mail->Body = $mail->Body . "<br>" . $exception->getMessage() . "<br>" ;
      $mail->Subject = $mail->Subject . " Etape $step";
      $mail->send();
   }
   return 1;
}

// Exception au démarrage
function exception_at_start($exception, $drive, $scenario)
{
	$mail = $driver->getNiceMail();
	// Prenons une copie d'écran à tout hasard....
	if (isset($driver)) $driver->takeSnapshot('Start', $scenario->getName());

	//Traitement du message d'exception pour le rendre plus lisible
	//On coupe la fin qui propose la documentation
	$ex_message = substr($exception->getMessage(), 0, strpos($exception->getMessage(),'  ('));
	$duration = '';
	if (strpos($exception->getMessage(),'milliseconds'))
	{
		$start = strpos($exception->getMessage(),'timeout:')+8;
		$end = strpos($exception->getMessage(),'milliseconds')+11;
		$duration = "La commande s'est terminée après".substr($exception->getMessage(),$start,$end-$start)."es.";
	}

	fwrite(STDERR, " \e[0;31m/!\ \e[0mLe scénario s'est arrêté\e[0m.\n\nSelenium a renvoyé cette erreur :\n\e[1;37m".$ex_message."\e[0m".$duration."\n\n");

	if (is_object($mail))
	{
		$mail->addBody("<h2 class='error'>Erreur à l'étape Démarrage du navigateur</h2><p>".$exception->getMessage()."</p>");
	}
	$error += 1;
	return $error;
}

// Exceptions "normales"
function exception_normale($exception, $driver, $scenario, $error)
{
	$mail = $driver->getNiceMail();
	// Prenons une copie d'écran à tout hasard....
	if (isset($driver)) $driver->takeSnapshot($scenario->getStep(), $scenario->getName());

	//Traitement du message d'exception pour le rendre plus lisible
	//On coupe la fin qui propose la documentation
	$ex_message = $exception->getMessage(); //substr($exception->getMessage(), 0, strpos($exception->getMessage(),'  ('));
	$duration = '';
	if (strpos($exception->getMessage(),'milliseconds'))
	{
		$start = strpos($exception->getMessage(),'timeout:')+8;
		$end = strpos($exception->getMessage(),'milliseconds')+11;
		$duration = "La commande s'est terminée après".substr($exception->getMessage(),$start,$end-$start)."es.";
	}

	fwrite(STDERR, " \e[0;31m/!\ \e[0mLe scénario s'est arrêté\e[0m.\n\nSelenium a renvoyé cette erreur :\n\e[1;37m".$ex_message."\e[0m".$duration."\n\n");

	if (is_object($mail))
	{
		$mail->addBody("<h2 class='error'>Erreur à l'étape ".$scenario->getStep()."</h2><p>".$exception->getMessage()."</p>");
//		$mail->Subject = $mail->Subject . " Etape $step";
	}
	$error += 1;
	return $error;
}
?>