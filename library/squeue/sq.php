<?php

include_once('SendQueueDefs.php');
include_once('SendQueue.php');
include_once('SendQueueHistory.php');
include_once('SendQueueTask.php');
include_once('SendQueueDB.php');
include_once('SendQueueAction.php');

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
		'stop',
		'count',
		'list',
		'service',
		'create:'
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

			case 'start':
			case 'stop':
				echo "done\n";
				break;

			case 'count':
				$q = new SendQueue();
				echo sprintf("waiting tasks: %d\n", $q->countPopQ());
				break;

			case 'list':
				$q = new SendQueue();
				echo $q->listPopQ() . "\n";
				break;

			case 'create':
				$v = strtoupper($v);
				switch ($v) {
					case 'TEST0':
						$task = new SendQueueTask(SendQueueDefs::ACTION_TEST0);
						$q = new SendQueue();
						$q->pushQ($task);
						echo sprintf("created, id = %d\n", $task->id);
						break;
					case 'TEST1':
						$task = new SendQueueTask(SendQueueDefs::ACTION_TEST1);
						$q = new SendQueue();
						$q->pushQ($task);
						echo sprintf("created, id = %d\n", $task->id);
						break;
					default:
						echo "not created\n";
						break;
				}

			case 'service':
				$q = new SendQueue();
				$q->popQ();
				echo "done\n";
				break;

		}

	}
	else {
		echo "wrong number of options or unknown options or missing values";
	}
}


// create global variables
$config = new SendQueueDefs();
$dbc = new SendQueueDB();

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

