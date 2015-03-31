<?php

class MultiProcessMailer {

	private $tasks;
	private $partitions = array();

	/**
	 * Starts multi processing
	 */
	public function startProcess() {

		// create log
		$log = new MultiProcessMailerLog();
		$log->putLine(MultiProcessMailerDefs::LOG_START);

		// read all waiting tasks
		$this->getTasks();
		// partition the list
		$this->createPartitions();
		// if any tasks found
		if ($ct = count($this->partitions)) {

			// create processes
			for ($i = 0; $i < $ct; $i++) {

				// fork processing
				$pid = pcntl_fork();

				switch($pid) {
					// no process created
					case -1:
						$log->putLine(MultiProcessMailerDefs::LOG_ERROR, MultiProcessMailerDefs::MESSAGE_NOFORK);
						$this->echoMessage(MultiProcessMailerDefs::MESSAGE_NOFORK);
						exit;
						// child created
					case 0:
						$this->echoMessage("in child $i");
						$send = new MultiProcessMailerMessage($i);
						$tasks = $this->partitions[$i];
						$send->startProcess($tasks);
						break;
						// still in parent
					default:
						break;
				}

			}

			// wait until all childs terminate
			while (pcntl_waitpid(0, $status) != -1) {
				$status = pcntl_wexitstatus($status);
				$this->echoMessage("child $status completed");
			}

		}

		$log->putLine(MultiProcessMailerDefs::LOG_STOP);
}

	/**
	 * Returns number of tasks for the calling moment
	 */
	public function countTasks() {
		global $dbc;
		$time = date('c', time());
		$query = 'select count(*) ct from ek_wysylka where status = 0 and data_wysylki <= "' . $time . '"';
		$ret = $dbc->select($query);
		return $ret[0]['ct'];
	}

	/**
	 * Creates test records in task table
	 */
	public function createTest() {
		global $dbc;
		global $config;
		$time = date('c', time());
		for ($i = 1; $i <= $config->test_count; $i++) {
			$query  = 'insert into ek_wysylka (adres, adres_dw, adres_udw, temat, tresc, data_dodania, data_wysylki) ';
			$query .= 'values ("' . $config->test_to . '", "' . $config->test_cc . '", "' . $config->test_bcc . '", "';
			$query .= $config->test_subject . '", "' . $config->test_body . '", "' . $time . '", "' . $time . '")';
			$dbc->execute($query);
		}
	}

	/**
	 * Clears the whole task table
	 */
	public function purgeTable() {
		global $dbc;
		$query = 'truncate table ek_wysylka';
		$dbc->execute($query);
	}

	/**
	 * Creates an array of all task identifiers
	 */
	private function getTasks() {
		global $dbc;
		$time = date('c', time());
		$query = 'select id from ek_wysylka where status = 0 and data_wysylki <= "' . $time . '"';
		$ret = $dbc->select($query);
		foreach ($ret as $v) {
			$this->tasks[] = (int)$v['id'];
		}
	}

	/**
	 * Creates partitions from task array
	 */
	private function createPartitions() {
		global $config;

		$part = 0;
		$ct = 0;
		if ($this->tasks) {
			foreach ($this->tasks as $v) {
				$this->partitions[$part][$ct++] = $v;
				if ($ct == $config->max_task_count) {
					$part++;
					$ct = 0;
				}
			}
		}
	}

	/**
	 * Prints a message onn stdout
	 * @param string $message
	 */
	private function echoMessage($message) {
		print $message . "\n";
	}



}

?>