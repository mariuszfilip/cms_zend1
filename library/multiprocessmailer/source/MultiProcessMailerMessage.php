<?php

/**
 * Interface to mail server
 * @author gustaf
 *
 */
class MultiProcessMailerMessage {

	private $pid;
	private $mailer;

	private $dbc;

	public function __construct($pid) {
		$this->pid = $pid;
	}

	/**
	 * Starts single process
	 * @param array $tasks array of identifiers
	 * @return integer exit status code
	 */
	public function startProcess($tasks) {
		// create own database connection
		$this->dbc = new MultiProcessMailerDB();
		$this->dbc->connect();

		// create mail server interface
		$this->createTransport();
		// loop thrue all tasks
		foreach ($tasks as $id) {
			// read it
			$task = $this->getTask($id);
			// send it
			$this->sendMessage($task);
		}
		// exit with code
		exit($this->pid);
	}

	/**
	 * Reads task from table
	 * @param int $id
	 * @return array
	 */
	private function getTask($id) {
		$query = 'select * from ek_wysylka where id = ' . $id;
		$row = $this->dbc->select($query);
		return $row[0];
	}

	/**
	 * Creates mail server interface
	 */
	private function createTransport() {

		global $config;

		$this->mailer = new PHPMailer();
		$this->mailer->IsSMTP();
		$this->mailer->SMTPAuth = true;

		$this->mailer->Host = $config->mailhost;
		$this->mailer->Username = $config->mailuser;
		$this->mailer->Password = $config->mailpass;

		// $this->mailer->SMTPSecure = $config->mailsec;
		// $this->mailer->Port = $config->mailport;

		$this->mailer->isHTML(true);
		$this->mailer->CharSet = $config->s_encoding;

		$this->mailer->From = $config->s_from;
		$this->mailer->FromName = $config->s_fromname;
	}

	/**
	 * Sends message
	 * @param array $task
	 */
	private function sendMessage($task) {

		// remove previously used addresses
		$this->mailer->ClearAddresses();
		$this->mailer->ClearCCs();
		$this->mailer->ClearBCCs();

		// prepare data
		$this->prepareAddress($task['adres']);
		$this->prepareCC($task['adres_dw']);
		$this->prepareBCC($task['adres_udw']);
		$this->mailer->Subject = $task['temat'];
		$this->mailer->Body = $task['tresc'];

		// try send it
		if ($this->mailer->Send()) {
			// success
			$this->updateStatus($task['id'], MultiProcessMailerDefs::STATUS_SENT);
		}
		else {
			/// failed
			$this->updateStatus($task['id'], MultiProcessMailerDefs::STATUS_ERROR, urlencode($this->mailer->ErrorInfo));
		}
	}

	/**
	 * Prepare TO
	 * @param string $address
	 */
	private function prepareAddress($address) {
		if ($list = $this->parseAddress($address))
			foreach ($list as $v)
				$this->mailer->AddAddress($v[0], $v[1]);
	}

	/**
	 * Prepare CC
	 * @param string $address
	 */
	private function prepareCC($address) {
		if ($list = $this->parseAddress($address))
			foreach ($list as $v)
				$this->mailer->AddCC($v[0], $v[1]);
	}

	/**
	 * Prepare BCC
	 * @param string $address
	 */
	private function prepareBCC($address) {
		if ($list = $this->parseAddress($address))
			foreach ($list as $v)
				$this->mailer->AddBCC($v[0], $v[1]);
	}

	/**
	 * Converts address spec in form of: "John Doe <jdoe@nospace.com>; Ivan Hoe <ivanhoe@anywhere.net" into list of addresses compliant with RFC2822
	 * @param string
	 * @return mixed false if no address found or array
	 */
	private function parseAddress($address) {
		global $config;
		$ret = false;

		// check if any address in string
		if (trim($address)) {
			// split into chuncks by semicolon
			$list = explode(';', $address);
			foreach ($list as $v) {
				// find left bracket
				$lbr = mb_strpos($v, '<', 0, $config->s_encoding);
				// find right bracket
				$rbr = mb_strpos($v, '>', 0, $config->s_encoding);
				// get string length
				$len = mb_strlen($v, $config->s_encoding);
				// if left bracket not found, it is address without preceding name
				if ($lbr === false) {
					$ret[] = array($v, '');
				}
				// bracket found, so extract name part and address part
				else {
					$m1 = trim(mb_substr($v, 0, $lbr - 1, $config->s_encoding));
					$m2 = trim(mb_substr($v, $lbr + 1, $rbr - $lbr - 1, $config->s_encoding));
					$ret[] = array($m2, $m1);
				}
			}
		}
		return $ret;
	}

	/**
	 * Updates task status in table
	 * @param integer $id
	 * @param integer $status
	 * @param string $message
	 */
	private function updateStatus($id, $status, $message = null) {
		$time = date('c', time());
		if (!$message) {
			$query = 'update ek_wysylka set status = ' . $status . ', data_wyslania = "' . $time . '" where id = ' . $id;
		}
		else {
			$query = 'update ek_wysylka set status = ' . $status . ', data_wyslania = "' . $time . '", status_komunikat = "' . $message . '" where id = ' . $id;
		}
		$this->dbc->execute($query);
	}
}

?>