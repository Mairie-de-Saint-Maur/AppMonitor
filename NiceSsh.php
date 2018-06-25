<?php
//////////////////////////////////////////////////////////////////
//                                                              //
//      Classe de gestion des connexions/commandes SSH          //
//                                                              //
//                STEPHAN Hugo 26-03-2018   V0.1                //
//                                                              //
//////////////////////////////////////////////////////////////////


class NiceSSH {
    // SSH Host
    private $ssh_host ; # =  Config::SSH_HOST;
    // SSH Port
    private $ssh_port ; # =  Config::SSH_PORT;
    // SSH Server Fingerprint
    private $ssh_server_fp ; # =  Config::SSH_FP;
    // SSH Username
    private $ssh_auth_user ; #=  Config::SSH_AUTH_USER;
    // SSH Public Key File
    private $ssh_auth_pub ; #=  Config::SSH_AUTH_PUB;
    // SSH Private Key File
    private $ssh_auth_priv ; #=  Config::SSH_AUTH_PRIV;
    // SSH Private Key Passphrase (null == no passphrase)
    private $ssh_auth_pass ; #= null ;
    // SSH Connection
    private $connection;
   
    public function connect($host) {
	$this->ssh_host  =  $host;
    	$this->ssh_port =  Config::$SSH_PORT;
	$this->ssh_server_fp =  Config::$SSH_FP;
	$this->ssh_auth_user =  Config::$SSH_AUTH_USER;
	$this->ssh_auth_pub =  Config::$SSH_AUTH_PUB;
	$this->ssh_auth_priv =  Config::$SSH_AUTH_PRIV;
	$this->ssh_auth_pass == null ;

		//Test de connection
        if (!($this->connection = ssh2_connect($this->ssh_host, $this->ssh_port))) {
            throw new Exception('Cannot connect to server');
        }
		//calcul de la FG
        $fingerprint = ssh2_fingerprint($this->connection,SSH2_FINGERPRINT_HEX);
       /* if (strcmp($this->ssh_server_fp, $fingerprint) !== 0) {
            throw new Exception('Unable to verify server identity!');
        }*/
		//test de la clé publique
        if (!ssh2_auth_pubkey_file($this->connection, $this->ssh_auth_user, $this->ssh_auth_pub, $this->ssh_auth_priv, $this->ssh_auth_pass)) {
            throw new Exception('Autentication rejected by server');
        }
    }
    public function exec($cmd) {
        if (!($stream = ssh2_exec($this->connection, $cmd))) {
            throw new Exception('SSH command failed');
        }
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream, 4096)) {
            $data .= $buf;
        }
        fclose($stream);
        return $data;
    }
    public function disconnect() {
        $this->exec('echo "EXITING" && exit;');
        $this->connection = null;
    }
    public function __destruct() {
        $this->disconnect();
    }
}
?>
