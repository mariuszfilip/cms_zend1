<?php

/**
 * Configuration and definitions
 * @author gustaf
 *
 */
class MultiProcessMailerDefs {

	// mail status values
	const STATUS_WAITS = 0;
	const STATUS_SENT = 1;
	const STATUS_ERROR = 2;

	// path and name of config file
	const CONFIGFILE		= './config.ini';

	// error messages
	const ERROR_CONFIGFILE	= 'config file write or read error';
	const ERROR_DBCONNECT	= 'database not connected';
	const ERROR_DBOPERATION	= 'database operation failed';

	// log ops
	const LOG_START = 'start';
	const LOG_STOP = 'stop';
	const LOG_ERROR = 'error';

	// error messages
	const MESSAGE_NOFORK = 'could not fork';

	// param values
	// database & log
	var $dbname;
	var $dbhost;
	var $dbuser;
	var $dbpass;
	var $log;

	// multi process factor
	var $max_task_count;

	// mail fields
	var $s_encoding;
	var $s_from;
	var $s_fromname;

	// mail server
	var $mailhost;
	var $mailuser;
	var $mailpass;
	var $mailport;
	var $mailsec;

	// test data
	var $test_to;
	var $test_cc;
	var $test_bcc;
	var $test_subject;
	var $test_body;
	var $test_count;


	/**
	 * Reads entire config file in class variables
	 */
	public function readConfig() {
		if ($file = file(MultiProcessMailerDefs::CONFIGFILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
			foreach ($file as $v) {
				if ($v && substr($v, 0, 1) != '#') {
					$pos = explode('=', $v);
					$varname = trim(strtolower($pos[0]));
					$varvalue = trim($pos[1]);
					$this->$varname = $varvalue;
				}
			}
			return true;
		}
		throw new Exception(MultiProcessMailerDefs::ERROR_CONFIGFILE);
	}

	/**
	 * Converts configuration to string
	 * @return string
	 */
	public function toString() {
		$s = '';
		$s .= 'dbname:            ' . $this->dbname . "\n";
		$s .= 'dbhost:            ' . $this->dbhost . "\n";
		$s .= 'dbuser:            ' . $this->dbuser . "\n";
		$s .= 'dbpass:            ' . $this->dbpass . "\n";
		$s .= 'log:               ' . $this->log . "\n";
		$s .= 'max_task_count:    ' . $this->max_task_count . "\n";

		$s .= 's_encoding:        ' . $this->s_encoding . "\n";
		$s .= 's_from:            ' . $this->s_from. "\n";
		$s .= 's_fromname:        ' . $this->s_fromname. "\n";

		$s .= 'mailhost:          ' . $this->mailhost . "\n";
		$s .= 'mailuser:          ' . $this->mailuser . "\n";
		$s .= 'mailpass:          ' . $this->mailpass . "\n";
		$s .= 'mailport:          ' . $this->mailport . "\n";
		$s .= 'mailsec:           ' . $this->mailsec . "\n";

		$s .= 'test_to:           ' . $this->test_to. "\n";
		$s .= 'test_cc:           ' . $this->test_cc. "\n";
		$s .= 'test_bcc:          ' . $this->test_bcc. "\n";
		$s .= 'test_subject:      ' . $this->test_subject. "\n";
		$s .= 'test_body:         ' . $this->test_body. "\n";
		$s .= 'test_count:        ' . $this->test_count. "\n";


		return $s;
	}

	/**
	 * Creates a line of configuration in form: name=value
	 * @param string $varname
	 * @param string $varvalue
	 * @return string
	 */
	private function getLine($varname, $varvalue) {
		return $varname . '=' . $varvalue . chr(13) . chr(10);
	}


}

?>