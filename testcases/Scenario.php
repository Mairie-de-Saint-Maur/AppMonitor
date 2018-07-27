<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//            classe générique de tests applicatifs             //
//                                                              //
//                   Blaise 30-01-2018   V0.1                   //
//                   Camus  26-04-2018   V0.2                   //
//                                                              //
//////////////////////////////////////////////////////////////////

class Scenario {
	protected $step;
	protected $driver;
	protected $name;
	protected $lock_file = null;
	protected $lock_filename = '';
	protected $steps = ['Home', 'Login', 'Action', 'Logout'];
   
   function __construct($driver) {
      $this->step = 'unset';
      $this->driver = $driver;
      $this->err = 0;
   }
   
   static function createScenario($driver, $name){

		// Instanciation de la classe de scénario
		//Vérification d'existence du scénario :
		Console("Vérification d'existence du scénario \e[1;33m$name\e[0m\n");
		if(file_exists ("testcases/$name.php")){
		   require_once("testcases/$name.php");
		}else{
		   Console("\e[0;31m /!\ ERREUR\e[0m : le fichier scénario \"\e[1;34m$name.php\e[0m\" n'a pas été trouvé.\n\n");
		   exit;
		}
		Console("[\e[0;32mOK\e[0m]\n\n");

		$scenario = new $name($driver);
		$scenario->setName($name);
		$scenario->setLockFile($name);
		
		return $scenario;
   }
   
	function isLocked(){
		if(file_exists($this->lock_filename)){
			
			//Le fichier existe, mais quand a-t-il été créé ?
			$this->lock_file = fopen($this->lock_filename, 'r') or die('Cannot open file:  '.$this->lock_filename);
			
			$date_ref = intval(fread($this->lock_file, filesize($this->lock_filename)));
			$date_exp = ($date_ref + Config::$LOCKFILE_MIN_EXPIRE);
			
			Console("Le précédent fichier LOCK n'a pas été effacé !\nDate fichier : ".date('Y-m-d H:i:s',$date_ref)." \nDate d'exp.  : ".date('Y-m-d H:i:s',$date_exp)."\nDate actuelle: ".date('Y-m-d H:i:s',mktime())."\n\n");
			
			if( mktime() > $date_exp){
				Console("Le précédent fichier LOCK a expiré, \e[0;31meffacement\e[0m et recréation.\n[\e[0;32mOK\e[0m]\n\n");
				//Le processus est sûrement bloqué, on efface le fichier
				$this->unlock();
				return false;
			}else{
				//le processus tourne encore, on skip ce scénario
				return true;
			}
		}else{
			//Le fichier n'existe pas donc le scénario n'est pas bloqué
			return false;
		}
	   
   }
   
   function getName(){
	   return $this->name;
   }
   
   function setName($name){
	   $this->name = $name;
   }
   
   /*** Crée le fichier lock et y inscrit la date de bloquage ***/
   function lock(){
		$this->lock_file = fopen($this->lock_filename, 'w') or die('Cannot open file:  '.$this->lock_filename);
		fwrite($this->lock_file , mktime());
		fclose($this->lock_file);
		return true;
   }
   
   /*** Ferme le fichier lock et le supprime ***/
	function unlock(){
		if(is_resource($this->lock_file)) fclose($this->lock_file);
		unlink($this->lock_filename);
		return false;
	}
   
   function getLockFile(){
	   return $this->lock_filename;
   }
   
   function setLockFile($name){
	   $this->lock_filename = Config::$LOCKFILE_FOLDER.$name.'.lock';
   }
   
   function getStep(){
	   return $this->step;
   }
   
   function getSteps(){
	   return $this->steps;
   }
   
   function setSteps(array $steps){
	   $this->steps = $steps;
   }
   
   function getDriver(){
	   return $this->driver;
   }
   
   function __destruct() {
	   $this->driver->close();
	   $this->driver->quit();
   }

   public function init_step($step) {
		if (!method_exists($this, $step)){
			Console( "\e[0;31m /!\ ERREUR\e[0m : le scénario ne contient pas d'étape \"\e[1;34m$step\e[0m\".\n\n");
			exit;
		}else{
			
			Console( "Etape \e[1;33m$step\e[0m");
			$this->step = $step;
			$this->err = 0;
		}
   }
   
   //public function 
}
?>
