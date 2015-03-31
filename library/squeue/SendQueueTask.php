<?php

/**
 * Holder for one queue task
 * @author gustaf
 *
 */
class SendQueueTask {

	var $id;
	var $queueDate;
	var $queueStatus;
	var $attempts;
	var $statusDate;
	var $actionDate;
	var $actionType;
	var $spec;

	/**
	 * Creates a simple task
	 * @param string $actionType
	 * @param string $spec
	 */
	public function __construct($actionType, $spec = '') {
		global $config;
		$now = time();

		$this->queueDate 		= date('c', $now);
		$this->queueStatus 		= SendQueueDefs::STATUS_WAIT;
		$this->attempts 		= 0;
		$this->statusDate 		= date('c', $now);
		$this->actionDate 		= date('c', $now + $config->delay * 60);
		$this->actionType 		= $actionType;
		$this->spec 			= $spec;
	}

	/**
	 * Converts an array into task object
	 * @param array $array
	 */
	public function fromArray($array) {
		$this->id				= $array['id'];
		$this->queueDate 		= $array['queue_date'];
		$this->queueStatus 		= $array['qstatus'];
		$this->attempts 		= $array['attempts'];
		$this->statusDate 		= $array['status_date'];
		$this->actionDate 		= $array['action_date'];
		$this->actionType 		= $array['action_type'];
		$this->spec 			= $array['spec'];
	}

	public function setStatus($status) {
		$this->queueStatus = $status;
	}

	public function setAttempts($attempts) {
		$this->attempts = $attempts;
	}
}

?>