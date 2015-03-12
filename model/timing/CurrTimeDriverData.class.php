<?php
namespace Model\Timing;

/**
 * Class CurrTimeRaceData
 * @package Event
 * Used to calculate one driver's race information for a specific time in a race.
 * Data to display in each row, and potential data for deciding its position
 */
class CurrTimeDriverData {
	public $pos;
	public $name;
	public $lapNo;
	public $lastLap;
	public $behind;
	public $lastTimeCrossLine;

	/**
	 *
	 * @param float $currTime
	 * @param RaceDriverData $rdd
	 */
	function __construct($currTime, $rdd) {
		$this->pos = 0; //unknown at this stage
		$this->behind = 0.0; //unknown at this stage
		
		$this->name = $rdd->name;

		$lap_i = 0;
		$lastTime = 0.0;
		$lastLapTime = 0.0;


		foreach ($rdd->lapData->lapTimeList as $laptime) {
			if (!empty($laptime)) {
				if (($lastTime + $laptime) > $currTime) {
					// found the one

					break;
				} else {
					$lap_i++;
					$lastTime = $lastTime + $laptime;
					$lastLapTime = $laptime;
				}
				$this->lapNo = $lap_i; //TODO: check +1 or not
				$this->lastLap = $lastLapTime;
				$this->lastTimeCrossLine = $lastTime;
			}
		}
	}
		
	
}