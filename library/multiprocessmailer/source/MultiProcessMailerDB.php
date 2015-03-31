<?php

/**
 * Database interface
 * @author gustaf
 *
 */
class MultiProcessMailerDB {

	private $link;
	private $id;

	/**
	 * Connects with db
	 * @throws Exception
	 * @return boolean
	 */
	public function connect() {
		global $config;
		if ($this->link = mysql_connect($config->dbhost, $config->dbuser, $config->dbpass, true)) {
			if ($db = mysql_select_db($config->dbname, $this->link)) {
				return true;
			}
			else
				throw new Exception(MultiProcessMailerDefs::ERROR_DBCONNECT);

		}
		else
		  throw new Exception(MultiProcessMailerDefs::ERROR_DBCONNECT);
	}


	/**
	 * Bye, bye
	 */
	public function disconnect() {
		mysql_close($this->link);
	}

	/**
	 * Returns newly generated record id
	 * @return number
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Execute an insert, update or delete
	 * @param string $query
	 * @throws Exception
	 * @return boolean
	 */
	public function execute($query) {
		if ($result = mysql_query($query, $this->link)) {
			$this->id = mysql_insert_id($this->link);
			return true;
		}
		else
			throw new Exception(MultiProcessMailerDefs::ERROR_DBOPERATION . ':' . mysql_error() . ':' . $query);

	}

	/**
	 * Executes a select and returns result as array
	 * @param string $query
	 * @throws Exception
	 * @return multitype:
	 */
	public function select($query) {
		if ($result = mysql_query($query, $this->link)) {
			$return = array();
			while ($row = mysql_fetch_assoc($result)) {
				$return[] = $row;
			}
			return $return;
		}
		else
			throw new Exception(MultiProcessMailerDefs::ERROR_DBOPERATION . ':' . mysql_error() . ':' . $query);
	}

}

?>