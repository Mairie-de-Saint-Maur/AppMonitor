<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//                 Classe de gestion des mails                  //
//                                                              //
//                LEJARRE Camus 24-04-2018   V0.1               //
//                                                              //
//////////////////////////////////////////////////////////////////
require_once("config.php");

class NiceMail extends PHPMailer{
	
	//Adresses email des destinataires par défaut
	private $default_dest ; # = Config::$DEFAULT_DEST;
	//Adresses email des expéditeurs par défaut
	private $default_reply ; #= Config::$DEFAULT_REPLY;
	
	/**
	 *  @brief Constructeur de la classe, paramètre les données de base du mail
	 *  
	 *  @param [in] $params['dest']  Array contenant un array pour chaque destinataire avec en index 0 le nom et en index 1 l'adresse
	 *  @param [in] $params['reply'] Array contenant un array pour chaque expéditeur avec en index 0 le nom et en index 1 l'adresse
	 *  @return Retourne un objet de la classe PHPMailer prérempli
	 *  
	 *  @details More details
	 */
	function __construct($params = array('dest'=>null, 'reply'=>null, 'exceptions'=>true)) {
		$this->default_dest = Config::$DEFAULT_DEST;
		$this->default_reply = Config::$DEFAULT_REPLY;
		$this->exceptions = $params['exceptions'];				// Passing `true` enables exceptions
		//Server settings
		$this->SMTPDebug = 0;                                 // Enable verbose debug output
		$this->isSMTP();                                      // Set mailer to use SMTP
		$this->Host =  Config::$SMTP;                 // Specify main and backup SMTP servers
		$this->SMTPAuth = false;                              // Disable SMTP authentication

		//Expediteur des mails
		$this->setFrom(Config::$DEFAULT_EXP_MAIL, Config::$DEFAULT_EXP_NAME);
		
		
		//var_dump($params['dest']);
		
		//Assignation des paramètres par défaut
		$params['dest'] = (isset($params['dest']))? $params['dest'] : $this->default_dest;
		$params['reply'] = (isset($params['reply']))? $params['reply'] : $this->default_reply;
		
		//Ajout des adresses
		foreach($params['dest'] as $dest){
			$this->AddAddress($dest[0], $dest[1]);
		}
		
		foreach($params['reply'] as $dest){
			$this->AddReplyTo($dest[0], $dest[1]);
		}
		//Content
		
		$this->CharSet = 'UTF-8';							 // Use UTF-8 Charset
		$this->setLanguage('fr', '/opt/AppMonitor/vendor/phpmailer/phpmailer/language/');
		$this->isHTML(true);                                  // Set email format to HTML
		$this->Subject = 'Echec scenario';
		$this->Body    = "<head><style type='text/css'>
							*{
								font-family:Arial,sans-serif;
							}
							.bold{
								font-weight: bold;
							}
							
							.info{
								color: dodgerblue;
							}
							.error{
								color: red;
							}
							
							.align_right{
								text-align: right;
							}
							
							.times_table{
								border-collapse: collapse;
							}
							.times_table td, .times_table th{
								border: solid 1px black;
								padding: 3px;
							}
							.times_table th{
								background-color: lightblue;
							}
							</style></head><body>";
		
		//return $this;
	}

	public function addBody($text)
	{
		$this->Body = $this->Body . $text;
	}
}
?>
