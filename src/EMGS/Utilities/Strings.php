<?php

namespace EMGS\Utilities {

	class Strings {
		public static function after($this, $inthat) {
			if (! is_bool ( strpos ( $inthat, $this ) ))
				return substr ( $inthat, strpos ( $inthat, $this ) + strlen ( $this ) );
		}
		public static function after_last($this, $inthat) {
			if (! is_bool ( strrevpos ( $inthat, $this ) ))
				return substr ( $inthat, strrevpos ( $inthat, $this ) + strlen ( $this ) );
		}
		public static function before($this, $inthat) {
			return substr ( $inthat, 0, strpos ( $inthat, $this ) );
		}
		public static function before_last($this, $inthat) {
			return substr ( $inthat, 0, strrevpos ( $inthat, $this ) );
		}
		public static function between($this, $that, $inthat) {
			return before ( $that, after ( $this, $inthat ) );
		}
		public static function between_last($this, $that, $inthat) {
			return after_last ( $this, before_last ( $that, $inthat ) );
		}
		
		// use strrevpos function in case your php version does not include it
		public static function strrevpos($instr, $needle) {
			$rev_pos = strpos ( strrev ( $instr ), strrev ( $needle ) );
			if ($rev_pos === false)
				return false;
			else
				return strlen ( $instr ) - $rev_pos - strlen ( $needle );
		}
		public static function str_contains($strings, $searchfor, $ignoreCase = false) {
			if ($ignoreCase) {
				$strings = strtolower ( $haystack );
				$searchfor = strtolower ( $searchfor );
			}
			$needlePos = strpos ( $strings, $searchfor );
			return ($needlePos === false ? false : ($needlePos + 1));
		}
	}
}

?>