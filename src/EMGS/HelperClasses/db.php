<?php

namespace EMGS\HelperClasses;

class db extends DBCrypt {
	private $mysqli;
	public $mysqliError;
	

	function __construct($database = null) {
		parent::__construct ();
		// connecting to database
		if ($database == 1 || $database == null) {
			$this->mysqli = new MYSQLi ( DB_HOST, DB_USER, DB_PASSWORD, DB_MOBILEAPPDB );
		} elseif ($database == 2) {
			$this->mysqli = new MYSQLi ( DB_HOST, DB_USER, DB_PASSWORD, DB_EMGSIHE_MOBILE );
		} else {
			return false;
		}
		$this->mysqli->set_charset ( 'utf8' );
		$this->mysqli->query ( "SET collation_connection = utf8_czech_ci" );
		
		if (mysqli_connect_errno ()) {
			$this->mysqliError = mysqli_connect_error ();
			return false;
		} else {
			return true;
		}
	}
	function __destruct() {
		$this->mysqli->close ();
	}
	function stmt_bind_assoc(&$stmt, &$out) {
		$data = mysqli_stmt_result_metadata ( $stmt );
		$fields = array ();
		$out = array ();
		
		$fields [0] = $stmt;
		$count = 1;
		
		while ( $field = mysqli_fetch_field ( $data ) ) {
			$fields [$count] = &$out [$field->name];
			$count ++;
		}
		call_user_func_array ( 'mysqli_stmt_bind_result', $fields );
	}
	public function stmt($rawQuery) {
		$stmt = $this->mysqli->prepare ( $rawQuery );
		if (mysqli_stmt_error ( $stmt )) {
			$this->mysqliError = mysqli_stmt_error ( $stmt );
			return false;
		}
		$stmt->execute ();
		$stmt->store_result ();
		return $stmt;
	}
	/**
	 * @return the $mysqli
	 */
	public function getMysqli() {
		return $this->mysqli;
	}
}

?>