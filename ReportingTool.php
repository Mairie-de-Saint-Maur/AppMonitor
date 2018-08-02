<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//      Classe de gestion des logs circulaires                  //
//                                                              //
//            Blaise 20-01-2018   V0.1                          //
//                                                              //
//////////////////////////////////////////////////////////////////
require_once('nsca/src/EonNsca.php');
require_once('NiceSsh.php');

class ReportingTool {
	private $ssh_connection1;
	private $ssh_connection2;

	private $rrdTool ; # =  Config::$RRD_TOOL;
	private $rrdUpdate ; #=  Config::$RRD_UPD;
	private $rrdFile ; #=  Config::$RRD_DEFAULT_FILE;

	private $nsca_client ;	
	private $nsca_msg ;
	private $nsca_state ;
	private $nsca_service ;
	private $driver;
	private $scenario;

	/* On n'utilise plus d'étapes définies, on se base sur les étapes demandées par le scénario
	public $timeHome    = 'U';
	public $timeLogin   = 'U';
	public $timeActions = 'U';
	public $timeLogout  = 'U';
	*/
	private $times = array();

   // Création du fichier RRD si nécessaire lors de l'instanciation
   function __construct($scenario, $driver) {
		$this->rrdTool =  Config::$RRD_TOOL;
		$this->rrdUpdate =  Config::$RRD_UPD;
		$this->rrdFile =  Config::$RRD_DEFAULT_FILE;

		$this->driver = $driver ;
		$this->scenario = $scenario ;

		$file = $this->scenario->getName();
		
	   //Préparation du client NSCA - envoi de commandes à NAGIOS
      $this->nsca_client = new EonNsca();
	  $this->nsca_msg = "UNKNOWN STATE" ;
	  $this->nsca_state = EonNsca::STATE_UNKNOWN;
	  $this->nsca_service = $file ;
	  
	  //Vérification de l'existance du fichier RRD et création si besoin
      $this->rrdFile = Config::$RRD_DIR.$file . ".rrd";
      if (!file_exists($this->rrdFile)) {
         $parameters = "--step 60 --no-overwrite DS:home:GAUGE:120:0:60000 DS:login:GAUGE:120:0:60000 DS:actions:GAUGE:120:0:60000 DS:logout:GAUGE:120:0:60000 RRA:AVERAGE:0.5:1:2880 RRA:AVERAGE:0.5:5:2304 RRA:AVERAGE:0.5:30:700 RRA:AVERAGE:0.5:120:775 RRA:AVERAGE:0.5:1440:3700 RRA:MIN:0.5:1:2880 RRA:MIN:0.5:5:2304 RRA:MIN:0.5:30:700 RRA:MIN:0.5:120:775 RRA:MIN:0.5:1440:3700 RRA:MAX:0.5:1:2880 RRA:MAX:0.5:5:2304 RRA:MAX:0.5:30:700 RRA:MAX:0.5:120:775 RRA:MAX:0.5:1440:3700 RRA:LAST:0.5:1:2880 RRA:LAST:0.5:5:2304 RRA:LAST:0.5:30:700 RRA:LAST:0.5:120:775 RRA:LAST:0.5:1440:3700";
         exec("$this->rrdTool create $this->rrdFile $parameters", $output, $errno);
         if ( $errno <> 0 ){
			 Console("\n\e[0;31m/!\ ERREUR RRD\e[0m ".$errno."");
			 if ($errno == 127) Console(" : Commande introuvable");
			 Console("\n\n");
			 if ($output) print_r($output);
		 }
         return $errno;
      }
   }

   // La destruction de la classe entraine l'enregistrement du log avec les valeurs par défaut
   // Cela permet de conserver la trace des plantages dans les données d'exécution
   function __destruct() {
	  $this->update();
	  //On met fin au lock par fichier sur le scénario
	  Console("\e[0;31mSuppression\e[0m du fichier LOCK\n");
	  $this->scenario->unlock();
	  Console("[\e[0;32mOK\e[0m]\n\n");
   }

	// Update du fichier rrd et du status nagios NSCA
	public function update()
	{
		Console("\e[1;34mEnvoi de la requête NSCA\e[0m : ".$this->nsca_service." -> ".$this->nsca_msg.' ('.$this->nsca_state.')');
		//Envoi de la requête
		$this->nsca_client->send('Applications', $this->nsca_service, $this->nsca_state, $this->nsca_msg);
		//Commande SSH pour écrire le statut dans le fichier .status -- Commentée parce qu'on lit maintenant les statuts depuis NAGIOS
		//$this->ssh_report();
		
		//Tableau des temps
		$mask = "|%-9.9s |%10.10s |\n";
		echo "\n\n|----------------------|\n";
		printf($mask,"Etape","Temps");
		echo "|----------------------|\n";
		
		$total_time = 0;
		$command_steps = '';
		$command_times = '';
		foreach($this->times as $step => $time){
			$time = (isset($time))? $time : 'U' ;
			$total_time += $time;
			if($command_steps != '') $command_steps .= ':';
			if($command_times != '') $command_times .= ':';
			$command_steps .= $step;
			$command_times .= $time;
			printf($mask,$step,($time != 'U') ? $time.' ms' : 'Timeout');
			
		}
		echo "|----------------------|\n";
		printf($mask, "TOTAL",($total_time) . " ms");
		echo "|----------------------|\n\n";
		
		//Composition de la commande avec la liste des étapes
		//exec("$this->rrdUpdate $this->rrdFile -t home:login:actions:logout N:$timeHome:$timeLogin:$timeActions:$timeLogout", $output, $
		//Commande dynamique - ATTENTION : l'objet RRD dans cacti ne permet que 4 étapes.
		exec("$this->rrdUpdate $this->rrdFile -t home:login:actions:logout N:$command_times", $output, $errno
		);
		if ( $errno <> 0 ){
			Console("\n\e[0;31m /!\ ERREUR RRD\e[0m ".$errno." : ");
			if ($errno == 127) Console(" : Commande introuvable");
			if ($output){
				if($errno == 1){
					Console(substr($output[11], 7)."\n");
				}else{
					Console("\n\n");
					print_r($output);
				}
			}
		}
		return $errno;
	}

   // Met à jour le statut et le message Nagios NSCA
   public function nsca_report($state, $msg)
   {
	   $this->nsca_state = $state ;
	   $this->nsca_msg = $msg;
   }
   
   /* REMPLACEE par la lecture des status depuis Nagios
   
   // Met à jour le statut sur le serveur distant
   public function ssh_report()
   {
	   $clear_state = "";
	   switch($this->nsca_state)
	   {
			case 0:
				$clear_state = "OK";
				break;
			case 1:
				$clear_state = "WARNING";
				break;
			case 2:
				$clear_state = "CRITICAL";
				break;
			case 3:
				$clear_state = "UNKNOWN";
				break;
	   }
	   
	   //commande d'écriture dans le fichier .status
	   $cmd = "echo '".$clear_state."' > ".Config::$STATUS_FILE_DIR."$this->nsca_service.status" ;
	   
	   //Exécution de la commande SSH
	   $this->ssh_connection1->exec($cmd);
	   $this->ssh_connection2->exec($cmd);
   }
   */
   /////////////////////////////
   //    SETTERS ET GETTERS   //
   /////////////////////////////
	
	function getTimes(){
		return $this->times;
	}
	
	function getTime($step){
		return $this->times[$step];
	}
	
	function setTime($step, $time){
		$this->times[$step] = $time;
	}
	
	function setSteps($steps){
		foreach($steps as $step){
			$this->times[$step] = 'U';
		}
	}
}
?>
