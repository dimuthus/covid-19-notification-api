<?php

namespace EMGS\Utilities;

class DateTime {
	function __construct() {
	}
	function __destruct() {
	}
	
public static 	function DateDiff(){
	$date1 = new \DateTime("2007-03-24");
	$date2 = new \DateTime("2009-06-26");
	$interval = $date1->diff($date2);
	
	echo "difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days ";
	
	// shows the total amount of days (not divided into years, months and days like above)
	echo "difference " . $interval->days . " days ";
}
}

?>