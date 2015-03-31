<?php

/**
 * Status history register
 * @author gustaf
 *
 */
class SendQueueHistory {

	/**
	 * Stores history position
	 * @param SendQueueTask $task
	 * @param string $description
	 * @return boolean
	 */
	public function storeHistory(SendQueueTask $task, $description = null) {
		global $dbc;
		$query	=	'insert into squeue_history (id_squeue, status, status_date, description) values (' .
					$task->id . ', ' .
					'"' . $task->queueStatus . '", ' .
					'"' . $task->statusDate . '", ' .
					'"' . $description . '"' .
					')';

		$dbc->execute($query);
		return true;
	}

}

?>