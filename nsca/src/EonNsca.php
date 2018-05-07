<?php

require_once('SendNsca.php');
use PhpSendNsca\SendNsca;

class EonNsca extends SendNsca
{
	public function __construct() 
	{
		parent::__construct('srv-eon.saintmaur.local');
	}
}
