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
		return $scenario;
   }
   
   function getName(){
	   return $this->name;
   }
   
   function setName($name){
	   $this->name = $name;
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
}
?>
