<?php

/**
 * Puts a message line in log file
 * @author gustaf
 *
 */
class MultiProcessMailerLog {

	public function putLine($op, $message = '') {
		global $config;
		$ff = fopen($config->log, 'a');
		$time = date('c', time());
		$line = '[' . $time . '] ' . $op . ' ' . $message . "\n";
		fwrite($ff, $line);
	}

}

?>