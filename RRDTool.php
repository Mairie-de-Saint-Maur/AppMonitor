<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//      Classe de gestion des logs circulaires                  //
//                                                              //
//            Blaise 20-01-2018   V0.1                          //
//                                                              //
//////////////////////////////////////////////////////////////////
class RRDTool {

   private $rrdTool = '/opt/rrdtool-1.7.0/bin/rrdtool';
   private $rrdUpdate = '/opt/rrdtool-1.7.0/bin/rrdupdate';
   private $rrdFile = 'default.rrd';

   public $timeHome    = 'U';
   public $timeLogin   = 'U';
   public $timeActions = 'U';
   public $timeLogout  = 'U';

   // Création du fichier RRD si nécessaire lors de l'instanciation
   function __construct($file) {
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

   // Update du fichier rrd
   public function update() {
      $timeHome = $this->timeHome;
      $timeLogin = $this->timeLogin;
      $timeActions = $this->timeActions;
      $timeLogout = $this->timeLogout;

      echo "Scenario $this->rrdFile\n"; 
      echo "Home:    $timeHome ms\n";
      echo "Login:   $timeLogin ms\n";
      echo "Actions: $timeActions ms\n";
      echo "Logout:  $timeLogout ms\n";
      echo "Total:   " . ($timeHome + $timeLogin + $timeActions + $timeLogout) . " ms\n\n";
      exec("$this->rrdUpdate $this->rrdFile -t home:login:actions:logout N:$timeHome:$timeLogin:$timeActions:$timeLogout", $output, $errno
);
      if ( $errno <> 0 ) print_r($output);
      return $errno;
   }

}
?>
