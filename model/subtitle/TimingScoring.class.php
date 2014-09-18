<?php

namespace Model\Subtitle;
use Model\Event;

class TimingScoring {
	/** @var Event\RaceResult */
	public $raceResult;
	
	/** @var float */
	public $startTime; // float in seconds
	
	/** @var int */
	public $raceTime; // int in minutes

	/** @var string */
	public $tags; // tag for ass subtitle format

	/**
	 * @param Event\RaceResult $rr
	 */
	function __construct($rr) {
		$this->raceResult = $rr;
		$this->raceTime = $this->getRaceTime();
		$this->tags = "\\an7";
	}

	/**
	 * @return string
	 */
	function __toString() {
		$retval = "";
		
		$totalCurrTime = $this->raceResult->getTotalCurrTime();
		//echo var_dump($totalCurrTime);
		
		$this->getRaceTime(); // this will setup $this->raceTime automatically
		
		foreach ($totalCurrTime as $i => $lineCross) {
			//var_dump($lineCross);
			$currTime = $lineCross['currTime'];
			$did = $lineCross['driverId'];
			$lap_i = $lineCross['lap_i'];
			
			// collect info
				
			// check if finished
			$finalLap = false;
			if ($currTime > ($this->raceTime * 60)) {
				$finalLap = true;
			}
			
			$headerString = "";
			$contentString = "";
			$contentFlashString = "";
			
			if (!$finalLap) {
				// header
				// Pos Name               LastLap Lap# Behind\N
				// 123 123456789012345678 1234567 1234 123456\N
				$headerString = "Pos Name               LastLap Lap# Behind\N";
				
				// driver info
				$ctrd = new Event\CurrTimeRaceData($currTime, $this->raceResult);
				foreach ($ctrd->currTimeDriverDataArr as $ctdd) { /* @var $ctdd Event\CurrTimeDriverData */

					if (!empty($ctdd->lapNo)) {

						$this_pos = $this->makeLengthString($ctdd->pos, 3, "right");
						$this_name = $this->makeLengthString($ctdd->name, 18, "left");
						$this_lastlap = $this->makeLengthString($ctdd->lastLap, 7, "right");
						$this_lapNo = $this->makeLengthString($ctdd->lapNo, 4, "right");
						$this_behind = $this->makeLengthString(($ctdd->behind == 0 ? "" : $ctdd->behind), 6, "left");

						if ($this->raceResult->getIdByName($ctdd->name) == $did) {
							$contentFlashString .= "_\N";
						} else {
							$contentFlashString .= "{$this_pos} {$this_name} {$this_lastlap} {$this_lapNo} {$this_behind}\N";
						}


						$contentString .= "{$this_pos} {$this_name} {$this_lastlap} {$this_lapNo} {$this_behind}\N";
					}
				}
				
			} else {
				// header
				// Pos Name               LastLap Lap# RaceTime  Behind FastLap Consistency\N
				// 123 123456789012345678 1234567 1234 123456789 123456 1234567 12345678901\N
				$headerString = "Pos Name               LastLap Lap# RaceTime  Behind FastLap Consistency\N";
				
				// driver info
				$ctrd = new Event\CurrTimeRaceData($currTime, $this->raceResult);
				//var_dump($ctrd->currTimeDriverDataArr);
				foreach ($ctrd->currTimeDriverDataArr as $ctdd) { /* @var $ctdd Event\CurrTimeDriverData */
					
					//var_dump($ctdd);
					if (!empty($ctdd->lapNo)) {

					
						$this_did = $this->raceResult->getIdByName($ctdd->name);

						$this_pos = $this->makeLengthString($ctdd->pos, 3, "right");
						$this_name = $this->makeLengthString($ctdd->name, 18, "left");
						$this_lastlap = $this->makeLengthString($ctdd->lastLap, 7, "right");
						$this_lapNo = $this->makeLengthString($ctdd->lapNo, 4, "right");
						$this_behind = $this->makeLengthString($this->raceResult->driverList[$this_did]->behind, 6, "left");

						$this_racetime = $this->makeLengthString($this->raceResult->driverList[$this_did]->totalTime, 9, "right");
						$this_fastlap = $this->makeLengthString($this->raceResult->driverList[$this_did]->lapData->getBestlap(true), 7, "right");
						$this_consistency = $this->makeLengthString($this->raceResult->driverList[$this_did]->lapData->getConsistency(true), 11, "left");

						if ($this_did == $did) {
							$contentFlashString .= "_\N";
						} else {
							$contentFlashString .= "{$this_pos} {$this_name} {$this_lastlap} {$this_lapNo} {$this_racetime} {$this_behind} {$this_fastlap} {$this_consistency}\N";
						}


						$contentString .= "{$this_pos} {$this_name} {$this_lastlap} {$this_lapNo} {$this_racetime} {$this_behind} {$this_fastlap} {$this_consistency}\N";
					}
				}
				
			}

			//var_dump($this->startTime);
			//var_dump($currTime);

			// check next cross line, if within 0.1s, deal with mid time
			if (isset($totalCurrTime[$i + 1])) {
				if ($totalCurrTime[$i + 1]['currTime'] - $currTime < 0.1) {
					$flashIntervalTime = $totalCurrTime[$i + 1]['currTime'] - $currTime;
				} else {
					$flashIntervalTime = 0.1;
				}
			}

			$lineStartTime = new TimeStamp($this->startTime + $currTime);
			$lineMidTime = new TimeStamp($this->startTime + $currTime + $flashIntervalTime);
			$lineEndTime = new TimeStamp($this->startTime + (isset($totalCurrTime[$i + 1]) ? $totalCurrTime[$i + 1]['currTime'] : $currTime + 10));
			$retval .= "Dialogue: 0,{$lineStartTime},{$lineMidTime},DefaultVCD,NTP,0,0,0,,{{$this->tags}}{$headerString}{$contentFlashString}\n";
			$retval .= "Dialogue: 0,{$lineMidTime},{$lineEndTime},DefaultVCD,NTP,0,0,0,,{{$this->tags}}{$headerString}{$contentString}\n";

			
		}
		
		return $retval;
	}

	/**
	 * Calculate time difference between the start of first subtitle, and the start of the video.
	 * Given any driver's cross line time (in vid) with that lap number (start with 1) and cross line time in result.
	 * @param int $driverId
	 * @param float $videoTime
	 * @param int $lapNumber
	 * @return float
	 */
	function calculateStartTime($driverId, $videoTime, $lapNumber = 1) {
		$raceTime = $this->raceResult->driverList[$driverId]->lapData->getLapTime($lapNumber-1);
		$diff = $videoTime - $raceTime;
		return $diff;
	}

	/**
	 * @param int $driverId
	 * @param float $videoTime
	 * @param int $lapNumber
	 */
	function setStartTime($driverId, $videoTime, $lapNumber = 1) {
		$this->startTime = $this->calculateStartTime($driverId, $videoTime, $lapNumber);
	}

	/**
	 * @return float
	 */
	function getStartTime() {
		if (!isset($this->startTime)) {
			$this->startTime = 0;
		}
		return $this->startTime;
	}

	/**
	 * Offical time for the race in minutes.
	 * This function check both setting's race time,
	 * if not set, check top finisher's finishing time.
	 * @return int
	 */
	function getRaceTime() {
		if (!isset($this->raceTime) || $this->raceTime == -1) {
			// find guessed one from race result
			if (count($this->raceResult->driverList) > 0) {
				$racewinner = $this->raceResult->driverList[0];
				if (isset($racewinner->totalTime) && !empty($racewinner->totalTime)) {
					$winnerTime = $racewinner->totalTime;
					if (strpos($winnerTime, ":") !== false) {
						// warning: only accept up to minutes
						$minutes = intval(strstr($winnerTime, ":", true));
						$this->raceTime = $minutes;
					}
				}
			}
		} 
		return $this->raceTime;
	}
	
	/**
	 * Manually setup raceTime if wanted.
	 * @param int $min
	 */
	function setRaceTime($min) {
		$this->raceTime = $min;
	}

	/**
	 * create a string at the correct length, if overlength, shorten and finish with "..."
	 * Initially designed for making driver names fit in field.
	 * @param string $str
	 * @param int $len
	 * @param string $align
	 * @return string
	 */
	function makeLengthString($str, $len, $align="right") {
		if (is_int($str) || is_float($str)) {
			$str = strval($str);
		}
		
		if (strlen($str) > $len) {
			$str = substr($str, 0, $len-3) . "...";
		}
		
		$whiteStr = "";
		for ($i = 0; $i < $len - strlen($str); $i++) {
			$whiteStr .= " ";
		}
		
		if ($align == "right") {
			return $whiteStr . $str;
		} else if ($align = "left") {
			return $str . $whiteStr;
		}
		
	}
}