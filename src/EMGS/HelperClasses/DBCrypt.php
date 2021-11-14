<?php

namespace EMGS\HelperClasses;

abstract class DBCrypt {
	private static $iv = 'vbjD@fsdGsjkl4W4'; // Same as in JAVA
	private static $key = '&)7*S6FM#*^%07$&'; // Same as in JAVA
	private static $td;
	function __construct() {
	}
	function encrypt($str) {
		self::$td = mcrypt_module_open ( 'rijndael-128', '', 'cbc', self::$iv );
		mcrypt_generic_init ( self::$td, self::$key, self::$iv );
		
		$encrypted =\mcrypt_generic ( self::$td, utf8_encode ( $this->pkcs5_pad ( $str ) ) );
		
		mcrypt_generic_deinit ( self::$td );
		mcrypt_module_close ( self::$td );
		
		return bin2hex ( $encrypted );
		// return base64_encode(bin2hex($encrypted));
	}
	function decrypt($encrypedText) {
		
		// $code = $this->hex2bin(base64_decode($code));
		$code = $this->hex2bin ( $encrypedText );
		self::$td =\mcrypt_module_open ( 'rijndael-128', '', 'cbc', self::$iv );
		mcrypt_generic_init ( self::$td, self::$key, self::$iv );
		
		$decrypted =\mdecrypt_generic ( self::$td, $code );
		
		mcrypt_generic_deinit ( self::$td );
		mcrypt_module_close ( self::$td );
		
		return trim ( $this->pkcs5_unpad ( utf8_decode ( $decrypted ) ) );
	}
	final private function hex2bin($hexdata) {
		$bindata = '';
		
		for($i = 0; $i < strlen ( $hexdata ); $i += 2) {
			$bindata .= chr ( hexdec ( substr ( $hexdata, $i, 2 ) ) );
		}
		
		return $bindata;
	}
	final private function pkcs5_pad($text) {
		// return $text;
		$blocksize = 16;
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	final private function pkcs5_unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text )) {
			return false;
		}
		
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad) {
			return false;
		}
		
		return substr ( $text, 0, - 1 * $pad );
	}
}

?>