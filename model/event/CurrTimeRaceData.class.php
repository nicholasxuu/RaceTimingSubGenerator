<?php
namespace Model\Event;

class CurrTimeRaceData {
	/**
	 * @var CurrTimeDriverData[]
	 */
	public $currTimeDriverDataArr;
	
	/**
	 * 
	 * @param float $currTime
	 * @param RaceResult $rr
	 */
	function __construct($currTime, $rr) {
		$this->currTimeDriverDataArr = array();
		foreach ($rr->driverList as $rdd) {
			$this->currTimeDriverDataArr[] = new CurrTimeDriverData($currTime, $rdd);
		}

		usort($this->currTimeDriverDataArr, array("Model\Event\CurrTimeRaceData", "cmp_pos"));

		$prevLapNo = 0;

		for ($pos = 1; $pos <= count($this->currTimeDriverDataArr); $pos++) {
			$this->currTimeDriverDataArr[$pos-1]->pos = $pos;
			if ($this->currTimeDriverDataArr[$pos-1]->lapNo != $prevLapNo) {
				$prevLapNo = $this->currTimeDriverDataArr[$pos-1]->lapNo;
			} else {
				
			}
		}

	}
	
	/**
	 * @param CurrTimeDriverData $a
	 * @param CurrTimeDriverData $b
	 * @return bool
	 */
	function cmp_pos($a, $b) {
		if (floor($a->lapNo) != floor($b->lapNo)) {
			return $b->lapNo >= $a->lapNo;
		} else {
			if ($a->lastTimeCrossLine != $b->lastTimeCrossLine) {
				return $a->lastTimeCrossLine >= $b->lastTimeCrossLine;
			}
		} 
		return $a->lastLap >= $b->lastLap; // last resort, shouldn't reach here
	}
	
	
}