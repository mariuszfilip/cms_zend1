<?php

include_once('MultiProcessMailerDefs.php');
include_once('MultiProcessMailerDB.php');
include_once('MultiProcessMailer.php');
include_once('MultiProcessMailerMessage.php');
include_once('MultiProcessMailerLog.php');

include_once('../library/PHPMailer_5.2.0/class.phpmailer.php');

/**
 * Ouputs exception message as json string
 * @param Exception $exception
 */
function handle_exception(Exception $exception) {

	$response = new stdClass();
	$response->response = get_class($exception) . ':' . $exception->getMessage();
	print (json_encode($response));
}

/**
 * Performs preparations: read config, connect with database
 * @return boolean true if succeeded
 */
function do_prepare() {
	global $config;

	try {
		$config->readConfig();
	}
	catch (Exception $exception) {
		handle_exception($exception);
		return false;
	}

	global $dbc;
	try {
		$dbc->connect();
	}
	catch (Exception $exception) {
		handle_exception($exception);
		return false;
	}

	return true;
}

/**
 * Main entry point
 * @param array $argv command line options
 */
function main($argv) {

	$shortopts = '';
	$longopts = array(
		'status',
		'start',
		'count',
		'config',
		'test',
		'purge'
	);
	// parse options
	$options = getopt($shortopts, $longopts);
	if (count($options) == 1) {
		reset($options);
		$k = key($options);
		$v = $options[$k];

		switch ($k) {
			case 'status':
				echo "running\n";
				break;

			case 'config':
				global $config;
				echo $config->toString();
				break;

			case 'start':
				$mpm = new MultiProcessMailer();
				$mpm->startProcess();
				echo "finished\n";
				break;

			case 'count':
				$mpm = new MultiProcessMailer();
				$count = $mpm->countTasks();
				echo sprintf("tasks: %d\n", $count);
				break;

			case 'test':
				$mpm = new MultiProcessMailer();
				$count = $mpm->createTest();
				echo "test created\n";
				break;

			case 'purge':
				$mpm = new MultiProcessMailer();
				$count = $mpm->purgeTable();
				echo "tasks removed\n";
				break;

		}
	}
	else {
		echo "wrong number of options or unknown options or missing values";
	}
}


// create global variables
$config = new MultiProcessMailerDefs();
$dbc = new MultiProcessMailerDB();

// start here
if (do_prepare()) {
	try {
		main($argv);
	}
	catch (Exception $exception) {
		handle_exception($exception);
	}
	$dbc->disconnect();
}


?>

