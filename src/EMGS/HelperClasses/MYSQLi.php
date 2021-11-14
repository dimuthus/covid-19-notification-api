<?php

namespace EMGS\HelperClasses;

class MYSQLi extends \mysqli {
	function __construct($host, $user, $pass, $db) {
		parent::__construct ( $host, $user, $pass, $db );
	}
}

?>