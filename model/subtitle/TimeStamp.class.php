<?php

namespace Model\Subtitle;

class TimeStamp {
	/** @var float */
	public $time;

	/**
	 * Default construtor.
	 * @param float $t
	 */
	function __construct($t) {
		$this->time = $t;
	}
	
	/**
	 * Get formatted time for ass subtitle start end time.
	 * @param float $input_time
	 * @return string
	 */
	function get_time($input_time) {
		$mse = str_pad(round( ( $input_time * 100 ) % 100 ), 2, "0",STR_PAD_LEFT);
		$sec = str_pad(floor($input_time % 60), 2, "0",STR_PAD_LEFT);
		$min = str_pad(floor(($input_time / 60) % 60), 2, "0",STR_PAD_LEFT);
		$hrs = floor($input_time / 3600);
		
		return "{$hrs}:{$min}:{$sec}.{$mse}";
	}

	/**
	 * @return string
	 */
	function __toString() {
		return $this->get_time($this->time);
	}
	
	
}