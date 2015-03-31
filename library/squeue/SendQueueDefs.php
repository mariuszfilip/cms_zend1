<?php

/**
 * Configuration and definitions
 * @author gustaf
 *
 */
class SendQueueDefs {

	// action types
	const ACTION_NONE 		= 'NONE';
	const ACTION_GENDOC 	= 'GENDOC';
	const ACTION_SENDMAIL 	= 'SENDMAIL';
	const ACTION_TEST1		= 'TEST1';
	const ACTION_TEST0		= 'TEST0';

	// path and name of config file
	const CONFIGFILE		= './../config.ini';

	// error messages
	const ERROR_CONFIGFILE	= 'config file write or read error';
	const ERROR_DBCONNECT	= 'database not connected';
	const ERROR_DBOPERATION	= 'database operation failed';

	// status values
	const STATUS_WAIT		= 'WAIT';
	const STATUS_ATTEMPT	= 'ATTEMPT';
	const STATUS_DONE		= 'DONE';
	const STATUS_FAIL		= 'FAIL';

	// param values
	var $dbname;
	var $dbhost;
	var $dbuser;
	var $dbpass;
	var $log;
	var $attempts;
	var $delay;
	var $alarm;


	/**
	 * Reads entire config file in class variables
	 */
	public function readConfig() {
		if ($file = file(SendQueueDefs::CONFIGFILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
			foreach ($file as $v) {
				$pos = explode('=', $v);
				$varname = trim(strtolower($pos[0]));
				$varvalue = trim(strtolower($pos[1]));
				$this->$varname = $varvalue;
			}
			return true;
		}
		throw new Exception(SendQueueDefs::ERROR_CONFIGFILE);
	}




	/**
	 * Stores the actual configuration
	 */
	public function storeConfig() {

		$config[] 	= $this->getLine('dbname', 		$this->dbname);
		$config[] 	= $this->getLine('dbhost', 		$this->dbhost);
		$config[] 	= $this->getLine('dbuser', 		$this->dbuser);
		$config[] 	= $this->getLine('dbpass', 		$this->dbpass);
		$config[] 	= $this->getLine('log', 		$this->log);
		$config[] 	= $this->getLine('attempts', 	$this->attempts);
		$config[] 	= $this->getLine('delay', 		$this->delay);
		$config[] 	= $this->getLine('alarm', 		$this->alarm);

		try {
			file_put_contents(SendQueueDefs::CONFIGFILE, $config);
			return true;
		}
		catch (Exception $exception) {
			throw new Exception(SendQueueDefs::ERROR_CONFIGFILE);
		}

	}

	private function getLine($varname, $varvalue) {
		return $varname . '=' . $varvalue . chr(13) . chr(10);
	}


}

?>