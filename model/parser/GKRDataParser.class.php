<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/8/14
 * Time: 7:34 PM
 */

namespace Model\Parser;
use Model\Event as Event;

class GKRDataParser {
	/** @var string[] */
	public $fileContent;

	/** @var Event\RaceResult */
	public $raceResult;

	function __construct($fileContent) {
		$this->raceResult = new Event\RaceResult("");
		$this->fileContent = $fileContent;

		$stage = "start";

		$currRaceDriverData = new Event\RaceDriverData();
		$top3racerCount = 0;
		$currracerId = 0;
		foreach ($this->fileContent as $line) {
			$cleanLine = trim($line);
			if (empty($cleanLine)) {
				if ($stage == "start") {
					// do nothing
				} else if ($stage == "restOfRacerContent") {
					// end of restOfRacerContent
					$stage = "lapTimeHeader";
				} else if ($stage == "lapTimeContent") {
					$stage = "lapTimeHeader";
				}
			} else {
				if ($stage == "start") {
					// collect race name
					$this->raceResult->name = $cleanLine;
					$stage = "win_by";
				} else if ($stage == "win_by") {
					$stage = "date";
				} else if ($stage == "date") {
					$stage = "winner";
				} else if ($stage == "winner") {
					$stage = "top3racer_ln0";
				} else if ($stage == "top3racer_ln0") {
					$top3racerCount++;
					$currRaceDriverData->finishPosition = $top3racerCount;
					$currRaceDriverData->carNum = strval($top3racerCount);
					$currRaceDriverData->name = $cleanLine;
					$stage = "top3racer_ln1";
				} else if ($stage == "top3racer_ln1") {
					$values = preg_split("/\s+/", $cleanLine);
					if (count($values) != 4) {
						echo "Error, line value doesn't have 4 components:";
						var_dump($values);
						exit;
					}
					$currRaceDriverData->bestLap = $values[0];
					$currRaceDriverData->totalLaps = $values[1];
					$currRaceDriverData->behind = $values[2];
					$currRaceDriverData->averageLap = $values[3];

					$stage = "top3racer_ln2";
				} else if ($stage == "top3racer_ln2") {
					// both info used only in GKR, useless for me
					$this->raceResult->addDriver($currRaceDriverData);
					$currRaceDriverData = new Event\RaceDriverData();

					if ($top3racerCount < 3) {
						$stage = "top3racer_ln0";
					} else {
						$stage = "restOfRacerHeader";
					}
				} else if ($stage == "restOfRacerHeader") {
					// it's fixed (hopefully), I don't care.
					$stage = "restOfRacerContent";
				} else if ($stage == "restOfRacerContent") {
					$values = preg_split("/\t+/", $cleanLine);
					if (count($values) != 7) {
						echo "Error, line value doesn't have 7 components:";
						var_dump($values);
						exit;
					}
					$currRaceDriverData->carNum = $values[0];
					$currRaceDriverData->finishPosition = intval($values[0]);
					$currRaceDriverData->name = trim($values[1]);
					$currRaceDriverData->bestLap = $values[2];
					$currRaceDriverData->behind = $values[3]; // careful, this is about gap from leader
					$currRaceDriverData->totalLaps = $values[4];
					$currRaceDriverData->averageLap = $values[5];
					// index=6 is RPM score, don't care
					$this->raceResult->addDriver($currRaceDriverData);
					$currRaceDriverData = new Event\RaceDriverData();
				} else if ($stage == "lapTimeHeader") {
					$currRacerName = $cleanLine;
					$currracerId = $this->raceResult->getIdByName($currRacerName);
					if ($currracerId === -1) {
						echo "current racer not found: \"{$currRacerName}\"";
						exit;
					}
					$stage = "lapTimeContent";
				} else if ($stage == "lapTimeContent") {
					$values = preg_split("/\s+/", $cleanLine);

					if (count($values) == 3) {
						if ($this->raceResult->driverList[$currracerId]->lapData->isEmpty()) {
							// fill a dummy first lap based on first lap position, GKR doesn't count "before" first real lap data.
							$tempPos = str_replace("[", "", str_replace("]", "", $values[2]));
							$this->raceResult->driverList[$currracerId]->lapData->addLapTime(floatval($tempPos));
						}
						$this->raceResult->driverList[$currracerId]->lapData->addLapTime(floatval($values[1]));
					}
					// otherwise, empty lap data for being lapped
				}
			}
		}

		//var_dump($this->raceResult);
	}
} 