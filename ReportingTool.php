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
   private $ssh_connection;
	
   private $rrdTool = '/opt/rrdtool-1.7.0/bin/rrdtool';
   private $rrdUpdate = '/opt/rrdtool-1.7.0/bin/rrdupdate';
   private $rrdFile = 'default.rrd';
   
   private $nsca_client ;	
   private $nsca_msg ;
   private $nsca_state ;
   private $nsca_service ;

   public $timeHome    = 'U';
   public $timeLogin   = 'U';
   public $timeActions = 'U';
   public $timeLogout  = 'U';


   // Création du fichier RRD si nécessaire lors de l'instanciation
   function __construct($file) {
      $this->nsca_client = new EonNsca();
	  $this->nsca_msg = "Selenium Web Test : UNKNOWN STATE" ;
	  $this->nsca_state = EonNsca::STATE_UNKNOWN;
	  $this->nsca_service = $file ;
	  
      $this->ssh_connection = new NiceSsh();
	  $this->ssh_connection->connect();
	  $this->ssh_connection->exec("mkdir -p /var/www/html/dev/listapp/app_status/");
      
      $this->rrdFile = $file . ".rrd";
      if (!file_exists($this->rrdFile)) {
         $parameters = "--step 60 --no-overwrite DS:home:GAUGE:120:0:60000 DS:login:GAUGE:120:0:60000 \
           DS:actions:GAUGE:120:0:60000 \
           DS:logout:GAUGE:120:0:60000 \
           RRA:AVERAGE:0.5:1:2880 \
           RRA:AVERAGE:0.5:5:2304 \
           RRA:AVERAGE:0.5:30:700 \
           RRA:AVERAGE:0.5:120:775 \
           RRA:AVERAGE:0.5:1440:3700 \
           RRA:MIN:0.5:1:2880 \
           RRA:MIN:0.5:5:2304 \
           RRA:MIN:0.5:30:700 \
           RRA:MIN:0.5:120:775 \
           RRA:MIN:0.5:1440:3700 \
           RRA:MAX:0.5:1:2880 \
           RRA:MAX:0.5:5:2304 \
           RRA:MAX:0.5:30:700 \
           RRA:MAX:0.5:120:775 \
           RRA:MAX:0.5:1440:3700 \
           RRA:LAST:0.5:1:2880 \
           RRA:LAST:0.5:5:2304 \
           RRA:LAST:0.5:30:700 \
           RRA:LAST:0.5:120:775 \
           RRA:LAST:0.5:1440:3700";
         exec("$this->rrdTool create $this->rrdFile $parameters", $output, $errno);
         if ( $errno <> 0 ) print_r($output);
         return $errno;
      }
   }

   // La destruction de la classe entraine l'enregistrement du log avec les valeurs par défaut
   // Cela permet de conserver la trace des plantages dans les données d'exécution
   function __destruct() {
      $this->update();
   }

   // Update du fichier rrd et du status nagios NSCA
   public function update() {
	  echo "NSCA Request : $this->nsca_service / $this->nsca_state / $this->nsca_msg\n" ;
	  $this->nsca_client->send('Applications', $this->nsca_service, $this->nsca_state, $this->nsca_msg);
	  
	  $this->ssh_report();
	  
      $timeHome = $this->timeHome;
      $timeLogin = $this->timeLogin;
      $timeActions = $this->timeActions;
      $timeLogout = $this->timeLogout;

      echo "Home:    $timeHome ms\n";
      echo "Login:   $timeLogin ms\n";
      echo "Actions: $timeActions ms\n";
      echo "Logout:  $timeLogout ms\n";
      echo "Total:   " . ($timeHome + $timeLogin + $timeActions + $timeLogout) . " ms\n";
      exec("$this->rrdUpdate $this->rrdFile -t home:login:actions:logout N:$timeHome:$timeLogin:$timeActions:$timeLogout", $output, $errno
);
      if ( $errno <> 0 ) print_r($output);
      return $errno;
   }

   // Met à jour le statut et le message Nagios NSCA
   public function nsca_report($state, $msg)
   {
	   $this->nsca_state = $state ;
	   $this->nsca_msg = $msg;
   }
   
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
	   
	   $cmd = "echo '".$clear_state."' > /var/www/html/dev/listapp/app_status/$this->nsca_service.status" ;
	   $this->ssh_connection->exec($cmd);
   }
}

?>
