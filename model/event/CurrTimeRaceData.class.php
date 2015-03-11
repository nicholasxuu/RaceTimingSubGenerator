<?php
namespace Model\Event;

/**
 * Class CurrTimeRaceData
 * @package Event
 * Used to calculate all driver's race information for a specific time in a race.
 */
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

		$prevLapNo = -1;
		for ($pos = 1; $pos <= count($this->currTimeDriverDataArr); $pos++) {
			$this->currTimeDriverDataArr[$pos-1]->pos = $pos;
			if ($this->currTimeDriverDataArr[$pos-1]->lapNo != $prevLapNo) {
				// if not on the same lap v.s. previous ppl crossed the line, then update lap no.
				$prevLapNo = $this->currTimeDriverDataArr[$pos-1]->lapNo;
			} else {
				// if on the same lap v.s. previous ppl, then update behind value.
				if (!is_null($prevLapNo)) {
					$this->currTimeDriverDataArr[$pos-1]->behind = round($this->currTimeDriverDataArr[$pos-1]->lastTimeCrossLine - $this->currTimeDriverDataArr[$pos-2]->lastTimeCrossLine, 3);
				}
			}
		}
	}
	
	/**
	 * @param CurrTimeDriverData $a
	 * @param CurrTimeDriverData $b
	 * @return bool
	 */
	function cmp_pos($a, $b) {
		if (is_null($a->lapNo) || is_null($a->lastTimeCrossLine) || is_null($a->lastLap)) {
			return true;
		}
		if (is_null($b->lapNo) || is_null($b->lastTimeCrossLine) || is_null($b->lastLap)) {
			return false;
		}
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