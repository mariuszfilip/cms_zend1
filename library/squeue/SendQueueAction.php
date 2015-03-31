<?php

/**
 * Wrapper for action tasks
 * @author gustaf
 *
 */
class SendQueueAction {

	/**
	 * Entry point
	 * @param SendQueueTask $task
	 * @return boolean
	 */
	public function perform(SendQueueTask $task) {
		switch ($task->actionType) {
			case SendQueueDefs::ACTION_GENDOC:
				return $this->performGendoc($task);
				break;
			case SendQueueDefs::ACTION_SENDMAIL:
				return $this->performSendmail($task);
				break;
			case SendQueueDefs::ACTION_TEST1:
				return true;
				break;
			case SendQueueDefs::ACTION_TEST0:
				return false;
				break;
			default:
				return false;
				break;
		}
		return false;
	}

	private function performGendoc(SendQueueTask $task) {
		return true;
	}

	private function performSendmail(SendQueueTask $task) {
		return true;
	}

}

?>