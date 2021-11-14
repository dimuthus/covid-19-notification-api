<?php

namespace EMGS\Security;

class Encryption {
	private static $iv; // Same as in JAVA
	private static $key; // Same as in JAVA
	private static $td;
	function __construct($iv, $key) {
		self::$iv = $iv;
		self::$key = $key;
	}
	function encrypt($str) {
		self::$td = mcrypt_module_open ( 'rijndael-128', '', 'cbc', self::$iv );
		mcrypt_generic_init ( self::$td, self::$key, self::$iv );
		
		$encrypted = mcrypt_generic ( self::$td, utf8_encode ( $this->pkcs5_pad ( $str ) ) );
		
		mcrypt_generic_deinit ( self::$td );
		mcrypt_module_close ( self::$td );
		
		return bin2hex ( $encrypted );
		// return base64_encode(bin2hex($encrypted));
	}
	function decrypt($encryptstr) {
		
		// $code = $this->hex2bin(base64_decode($code));
		$encryptstr = $this->hex2bin ( $encryptstr );
		self::$td = mcrypt_module_open ( 'rijndael-128', '', 'cbc', self::$iv );
		mcrypt_generic_init ( self::$td, self::$key, self::$iv );
		
		$decrypted = mdecrypt_generic ( self::$td, $encryptstr );
		
		mcrypt_generic_deinit ( self::$td );
		mcrypt_module_close ( self::$td );
		
		return trim ( $this->pkcs5_unpad ( utf8_decode ( $decrypted ) ) );
	}
	protected function hex2bin($hexdata) {
		$bindata = '';
		
		for($i = 0; $i < strlen ( $hexdata ); $i += 2) {
			$bindata .= chr ( hexdec ( substr ( $hexdata, $i, 2 ) ) );
		}
		
		return $bindata;
	}
	protected function pkcs5_pad($text) {
		// return $text;
		$blocksize = 16;
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	protected function pkcs5_unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		
		return substr ( $text, 0, - 1 * $pad );
	}
}

if (isset ( $_REQUEST ["test"] ) && $_REQUEST ["test"] == 'Yes') {
	
	$apiCrypt = new APICrypt ( IV, SECRET_KEY );
	echo IV . "   " . SECRET_KEY;
	echo $apiCrypt->encrypt ( "inam" );
	
	echo $api_response_message = $apiCrypt->decrypt ( $apiCrypt->encrypt ( "inam" ) );
	
	// $dbf= new DB_Functions;
	// echo $username = 'emgs1'; $password = 'abc@123'; $fullname = 'EMGS Office 01';
	// echo $dbf->storeUser ( $username, $password, $fullname,1 );
}

?>