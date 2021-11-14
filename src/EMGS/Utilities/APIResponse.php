<?php

namespace EMGS\Utilities;

use EMGS\HelperClasses\db;

class APIResponse {	

	function __construct() {
	}
	
	public function getAPIResponseMessage($message_code){
		$db = new db ( 1 );
		$rawQuery = "SELECT `message_title`,`message_details` FROM `api_response_message` WHERE `message_code`='".$message_code."'";		
		$stmt = $db->stmt( $rawQuery ); 
		$no_of_rows = $stmt->num_rows;
		$message_title='';
		$message_details='';
		if ($no_of_rows > 0) {
			$stmt->bind_result ( $message_title, $message_details );
			$row = array ();				
			while ( $stmt->fetch () ) {
				$row ['message_title'] = $message_title;
				$row ['message_details'] = $message_details;
				
			}
			$stmt->free_result ();
			$stmt->close ();			
			return $row;
		}else {
			return false;
		}
	}
	
	function __destruct() {
		 
	}	 
}

?>