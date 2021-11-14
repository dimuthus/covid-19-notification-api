<?php

namespace EMGS\Utilities;

use EMGS\HelperClasses\db;

class DBFunctions {	

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
	
	public function storeikad_tagAPI($tag_id, $passport, $nationality, $nat_code, $isdeleted, $entrydate, $enteredby) {
		$db = new db ( 1 );
		if ($this->isRowExisted ( "ikad_tag", "tag_id,passport", "tag_id='" . $tag_id . "'" ) == FALSE) {
			$query = "INSERT INTO ikad_tag( tag_id, passport,nationality,nat_code,isdeleted,entrydate,enteredby) VALUES('$tag_id', '$passport', '$nationality','$nat_code',$isdeleted,'$entrydate',$enteredby)";
			$stmt=$db->stmt($query);
				
			if ($stmt) {	
				$stmt->execute ();	
				return false;  
			}else{
				return 'error';
			}
		} else {
			return 'This ikad details are already exists! ';
		}
	}
	
	public function isRowExisted($table, $fields, $wherecond) {
		$db = new db ( 1 );
		$qry = "SELECT " . $fields . " FROM " . $table . " WHERE " . $wherecond;
		$stmt=$db->stmt($qry);
		$no_of_rows = $stmt->num_rows;
		
		if ($no_of_rows > 0) {
			// user existed
			return true;
		} else {
			// user not existed
			return false;
		}
	}
	
	function __destruct() {
		 
	}	 
}

?>