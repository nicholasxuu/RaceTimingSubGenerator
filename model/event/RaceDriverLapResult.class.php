<?php

namespace Model\Event;

class RaceDriverLapResult {
	/** @var float[] */
	public $lapTimeList;

	/** @var bool  */
	public $isDirty;

	/** @var float  */
	public $bestLap;

	/** @var float  */
	public $consistency;

	function __construct(/*...*/) {
		$this->lapTimeList = array();
		$this->isDirty = false;
		$this->bestLap = 0.0;
		$this->consistency = 0.0;
	}
	
	/**
	 * Add a lap time to the end of laptime array
	 * @param float $lapTime
	 */
	function addLapTime($lapTime) {
		$this->lapTimeList[] = $lapTime;
		$this->isDirty = true;
	}
	
	/**
	 * Insert a lap time to any position of the laptime array.
	 * @param int $lapNum
	 * @param float $lapTime
	 */
	function insertLapTime($lapNum, $lapTime) {
		// TODO: check if $lapNum is oversized
		array_splice($this->lapTimeList, $lapNum, 0, array($lapTime));
		$this->isDirty = true;
	}
	
	/**
	 * Get laptime of a particular lap
	 * @param int $lapNum
	 * @return float
	 */
	function getLapTime($lapNum) {
		// TOOD: handle exception, check lapNum is oversized
		return $this->lapTimeList[$lapNum];
	}
	
	/**
	 * @param string $prop
	 */
	function __get($prop) {
		if ($this->isDirty) {
			$this->getConsistency();
			$this->getBestlap();
			$this->isDirty = false;
		}
		return $this->$prop;
	}
	
	/**
	 * function made for NCHDataParser, keep even index lap times and remove odd index lap times. 
	 */
	function removeEveryOtherLapTime() {
		$temp = array();
		for ($i = 0; $i < count($this->lapTimeList); $i += 2) {
			$temp[] = $this->lapTimeList[$i]; 
		}
		$this->lapTimeList = $temp;
		$this->isDirty = true;
	}

	/**
	 * @param bool $ignoreFirst
	 * @return float
	 */
	function getAverage($ignoreFirst = true) {
		$start_i = 0;
		if ($ignoreFirst) {
			$start_i = 1;
		}
		
		$sum = 0;
		for ($i = $start_i; $i < count($this->lapTimeList); $i++) {
			$sum += floatval($this->lapTimeList[$i]);
		}
		return $sum / count($this->lapTimeList);
	}

	/**
	 * @param bool $ignoreFirst
	 * @return float
	 */
	function getBestlap($ignoreFirst = true) {
		$tempList = $this->lapTimeList;
		if ($ignoreFirst) {
			array_shift($tempList);
		}
		$this->bestLap = min($tempList);
		return $this->bestLap;
	}

	/**
	 * @param bool $ignoreFirst
	 * @return float
	 */
	function getConsistency($ignoreFirst = true) {
		$average = $this->getAverage($ignoreFirst);
		
		$start_i = 0;
		if ($ignoreFirst) {
			$start_i = 1;
		}
		
		$sum = 0;
		for ($i = $start_i; $i < count($this->lapTimeList); $i++) {
			$sum += pow( ($this->lapTimeList[$i] - $average) , 2);
		}
		$std_dev = sqrt($sum / (count($this->lapTimeList) - start_i));

		$this->consistency = 100 - (floatval(intval(($std_dev/$average * 10000))) / 100);
		
		return $this->consistency;
	}

	/**
	 * @return bool
	 */
	function isEmpty() {
		return empty($this->lapTimeList);
	}
	
} 