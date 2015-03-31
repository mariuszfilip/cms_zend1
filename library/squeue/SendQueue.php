<?php

/**
 * Main class
 * @author gustaf
 *
 */
class SendQueue {

	/**
	 * Place a task in queue
	 * @param SendQueueTask $task
	 * @return integer identifier of the created task
	 */
	public function pushQ(SendQueueTask $task) {
		global $dbc;
		$query	=	'insert into squeue (queue_date, qstatus, attempts, status_date, action_date, action_type, spec) values (' .
				 	'"' . $task->queueDate . '", ' .
					'"' . $task->queueStatus . '", ' .
					$task->attempts . ', ' .
					'"' . $task->statusDate . '", ' .
					'"' . $task->actionDate . '", ' .
					'"' . $task->actionType . '", ' .
					'"' . $task->spec . '"' .
					')';

		$dbc->execute($query);
		$task->id = $dbc->getId();
		$hh = new SendQueueHistory();
		$hh->storeHistory($task);
		return $task->id;
	}

	/**
	 * Perform actions on specified tasks
	 * @param array $filter
	 * @return multitype:
	 */
	public function popQ($filter = null) {
		global $dbc;
		$action_date = date('c', time());
		$query = 'select * from squeue where qstatus in ("WAIT", "ATTEMPT") and action_date <= "' . $action_date . '"';
		if ($queue = $dbc->select($query)) {
			$this->serviceQ($queue);
		}
		return $queue;
	}

	/**
	 * Count waiting tasks
	 * @param array $filter
	 * @return number
	 */
	public function countPopQ($filter = null) {
		global $dbc;
		$action_date = date('c', time());
		$query = 'select * from squeue where qstatus in ("WAIT", "ATTEMPT")';
		if ($queue = $dbc->select($query)) {
			return count($queue);
		}
		return 0;
	}

	/**
	 * Lists waiting tasks
	 * @param array $filter
	 * @return string|boolean
	 */
	public function listPopQ($filter = null) {
		global $dbc;
		$action_date = date('c', time());
		$query = 'select * from squeue where qstatus in ("WAIT", "ATTEMPT")';
		if ($queue = $dbc->select($query)) {
			$ret = '';
			foreach ($queue as $v) {
				$ret .= implode(',', $v) . "\n";
			}
			return $ret;
		}
		return false;
	}

	/**
	 * Performs actions on selected tasks
	 * @param array $queue
	 */
	private function serviceQ($queue) {
		$task = new SendQueueTask(SendQueueDefs::ACTION_NONE);
		foreach ($queue as $v) {
			$task->fromArray($v);
			$this->attemptAction($task);
		}
	}

	/**
	 * Performs action on single task
	 * @param SendQueueTask $task
	 */
	public function attemptAction(SendQueueTask $task) {
		global $config;
		$action = new SendQueueAction();

		if ($action->perform($task)) {
			$task->setStatus(SendQueueDefs::STATUS_DONE);
			$this->updateStatus($task);
		}
		else {
			if ($task->attempts == $config->attempts) {
				$task->setStatus(SendQueueDefs::STATUS_FAIL);
				$this->updateStatus($task);
			}
			else {
				$task->setAttempts($task->attempts + 1);
				$this->updateAttempts($task);
			}
		}
	}

	/**
	 * Changes status of the task
	 * @param SendQueueTask $task
	 * @return boolean
	 */
	public function updateStatus(SendQueueTask $task) {
		$this->updateTask($task);
		$hh = new SendQueueHistory();
		$hh->storeHistory($task);
		return true;
	}

	/**
	 * Changes attempts count of the task
	 * @param SendQueueTask $task
	 * @return boolean
	 */
	public function updateAttempts(SendQueueTask $task) {
		$this->updateTask($task);
		return true;
	}

	/**
	 * Stores changed values in db
	 * @param SendQueueTask $task
	 * @return boolean
	 */
	private function updateTask(SendQueueTask $task) {
		global $dbc;
		$query = 'update squeue set qstatus = "' . $task->queueStatus . '", attempts = ' . $task->attempts . ' where id = ' . $task->id;
		$dbc->execute($query);
		return true;
			}

}

?>