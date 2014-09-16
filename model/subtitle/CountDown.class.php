<?php
namespace Model\Subtitle;

class CountDown {
	/** @var float (in seconds) */
	public $startTime;

	/** @var int (in minutes) */
	public $totalMinutes;

	/** @var string */
	public $colorCode;

	/** @var string */
	public $styleCode;

	/**
	 * Default constructor
	 * @param int $totalMinutes
	 * @param float $startTime
	 */
	function __construct($totalMinutes = 8, $startTime = 0.0) {
		$this->startTime = $startTime;
		$this->totalMinutes = $totalMinutes;
		$this->colorCode = "\\c&H00FF00&";
		$this->styleCode = "\\an8\\fscx200\\fscy200\\fnQuartz MS";
	}

	/**
	 * Convert int in seconds to count down time stamp.
	 * @param int $input_time
	 * @return string
	 */
	function getCurrentCountdownStr($input_time) {
		$sec = str_pad(floor($input_time % 60), 2, "0",STR_PAD_LEFT);
		$min = floor(($input_time / 60) % 60);
		$hrs = floor($input_time / 3600);
		if ($hrs > 0) {
			$min = str_pad($min, 2, "0",STR_PAD_LEFT);
			return "{$hrs}:{$min}:{$sec}"; 
		}		
		return "{$min}:{$sec}";
	}

	/**
	 * @return string
	 */
	function __toString() {
		$output = "";
		for ($i = 0; $i < $this->totalMinutes * 60; $i++) {
			//echo var_dump($this->startTime + $i);
			$startTime = new TimeStamp($this->startTime + $i);
			$endTime = new TimeStamp($this->startTime + $i + 1);
			$countDownTime = $this->getCurrentCountdownStr($this->totalMinutes * 60 - $i);
			//echo var_dump("Dialogue: 0,{$startTime},{$endTime},DefaultVCD,NTP,0,50,10,,{{$this->colorCode}{$this->styleCode}}{$countDownTime}\n");
			$output .= "Dialogue: 0,{$startTime},{$endTime},DefaultVCD,NTP,0,50,10,,{{$this->colorCode}{$this->styleCode}}{$countDownTime}\n";
		}
		return $output;
	}
}