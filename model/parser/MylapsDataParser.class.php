<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 9/17/14
 * Time: 3:59 PM
 */

namespace Model\Parser;
use Model\Event;
use Model\Common;


class MylapsDataParser {
	/** @var Event\TotalResult */
	public $totalResult;

	/** @var string[] */
	public $fileContent;

	/** @var float */
	public $minLegalLaptime; // to setup minimum valid laptime.

	function __construct($fileContent) {
		$this->fileContent = $fileContent;
		$this->totalResult = new Event\TotalResult();
		$this->minLegalLaptime = 0;



		$this->totalResult->addRace("RaceName");


		$stage = "start";

		/*
			$this->name = "";
			$this->nickname = "";
			$this->bestLap = '';
			$this->totalLaps = '';
			$this->totalTime = '';
			$this->carNum = '';
			$this->behind = '';
			$this->averageLap = '';
			$this->finishPosition = 0;
			$this->lapData = new RaceDriverLapResult();
		 */
		foreach ($this->fileContent as $line) {
			$cleanLine = trim($line);
			if (empty($cleanLine)) {
				//$stage = "laptimes";
			} else {

				if ($stage == "start") { // general info area
					// check if there's minimum legal laptime setting, if so, setup member var
					if (strstr($cleanLine, "MinimumLegalLaptime") === 0) {
						$lineArr = explode("=", $cleanLine);
						if (count($lineArr) > 1 && !empty($lineArr[1])) {
							$this->minLegalLaptime = floatval($lineArr[1]);
							continue;
						}
					}
					if (strpos($cleanLine, "Pos") === 0) {
						$stage = "general_info";
						continue;
					}

				} else if ($stage == "general_info") {
					$lineArr = explode("\t", $cleanLine);
					if ($lineArr[0] == "#") {
						$stage = "laptimes_content";
						continue;
					}
					/*
						0   Pos
						1	No.
						2	Name
						3	Laps
						4	Diff
						5	Gap
						6	Total Tm
						7	Best Tm
						8	In Lap
						9	Nat/State
						10	Sponsor
					 */

					$currRaceDriverData = new Event\RaceDriverData();
					$currRaceDriverData->name = $lineArr[2];
					$currRaceDriverData->bestLap = Common\Globals::convertTimeToSeconds($lineArr[7]);
					$currRaceDriverData->totalLaps = $lineArr[3];
					$currRaceDriverData->carNum = $lineArr[1];
					$currRaceDriverData->behind = trim($lineArr[5], " -");
					$currRaceDriverData->finishPosition = $lineArr[0];
					$this->totalResult->raceResultList[0]->addDriver($currRaceDriverData);
					continue;
				} else if ($stage == "laptimes") {
					/*
						0	#
						1	No.
						2	Name
						3	Laps
						4	Lead
						5	Lap Tm
						6	Elapsed Tm
						7	Time of Day
						8	Hits
						9	Strength
						10	Tx
					 */
					$lineArr = explode("\t", $cleanLine);
					if ($lineArr[0] == "#") {
						$stage = "laptimes_content";
					}
					continue;
				} else if ($stage == "laptimes_content") {
					$lineArr = explode("\t", $cleanLine);
					if ($lineArr[2] == "Green Flag" && $lineArr[3] == "") {
						// ignore this line
						continue;
					} else {
						$currracerId = intval($lineArr[1]);
						if (preg_match('/[0-9]+/', $lineArr[5])) {
							$laptime = Common\Globals::convertTimeToSeconds($lineArr[5]);
						} else {
							$laptime = Common\Globals::convertTimeToSeconds($lineArr[6]);
						}
						$this->totalResult->raceResultList[0]->driverList[$this->totalResult->raceResultList[0]->getIdByCarNum($currracerId)]->lapData->addLapTime($laptime, $this->minLegalLaptime);
					}

				}
			}
		}

	}

} 