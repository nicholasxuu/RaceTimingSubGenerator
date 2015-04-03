<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/8/14
 * Time: 7:34 PM
 */

namespace Model\Parser;
use Model\Event as Event;

class LivetimeRCDataParser {
	/** @var string[] */
	public $fileContent;

	/** @var Event\TotalResult */
	public $totalResult;

	/**
	 * @param string[] $columns
	 * @return bool
	 */
	function is_race_name_row($columns) {
		foreach ($columns as $co) {
			if (strstr($co, "Mains :: Race") !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string[] $columns
	 * @return string
	 */
	function get_race_name($columns) {
		foreach ($columns as $co) {
			if (!empty($co)) {
				return $co;
			}
		}
	}

	/**
	 * @param string[] $columns
	 * @return bool
	 */
	function is_race_info_header($columns) {
		$patternList = array(
			"Driver Name", "Car", "Result", "Fastest",
		);
		foreach ($columns as $co) {
			foreach ($patternList as $i=>$pattern) {
				if (strstr($co, $pattern) !== false) {
					unset($patternList[$i]);
					break;
				}
			}
		}
		return empty($patternList);
	}

	/**
	 * @param string[] $columns
	 * @return bool
	 */
	function is_lap_data_header($columns) {
		$patternList = array(
			"/Car/", '/^\d+$/',
		);
		foreach ($columns as $co) {
			foreach ($patternList as $i=>$pattern) {
				if (preg_match($pattern, $co)) {
					unset($patternList[$i]);
					break;
				}
			}
		}
		return empty($patternList);
	}

	function get_lap_time($timeStr) {
		$clean = trim($timeStr, "\"");
		$lines = explode("\r\n", $clean);
		if (count($lines) > 0) {
			$sec = explode("/", $lines[0]);
			if (count($sec) > 1) {
				return $sec[1];
			}
		}
		return "";
	}

	function process_table($currentTable) {
		//var_dump($currentTable);

		// TODO: analyze table for pattern and collect data from the table.

		if ($this->is_race_name_row($currentTable[0])) {
			$this->raceResult = new Event\RaceResult($this->get_race_name($currentTable[0]));
		} else if ($this->is_race_info_header($currentTable[0])) {
			$header = $currentTable[0];

			for ($i = 1; $i < count($currentTable); $i++) {
				$currRaceDriverData = new Event\RaceDriverData();

				for ($j = 0; $j < count($currentTable[$i]); $j++) {
					if (isset($header[$j]) && $header[$j] != "") {
						if ($header[$j] == "Driver Name") {
							$currRaceDriverData->name = $currentTable[$i][$j];
						} else if ($header[$j] == "Car") {
							$currRaceDriverData->carNum = $currentTable[$i][$j];
						} else if ($header[$j] == "Result") {
							$result = explode("/", $currentTable[$i][$j]);
							$currRaceDriverData->totalLaps = $result[0];
							$currRaceDriverData->totalTime = $result[1];
						} else if ($header[$j] == "Fastest") {
							$currRaceDriverData->bestLap = $currentTable[$i][$j];
						}
						// ignored other columns
					}
				}

				$this->raceResult->addDriver($currRaceDriverData);
			}

		} else if ($this->is_lap_data_header($currentTable[0])) {
			$header = $currentTable[0];

			for ($i = 2; $i < count($currentTable); $i++) {
				for ($j = 0; $j < count($currentTable[$i]); $j++) {
					if (isset($header[$j]) && $header[$j] != "" && preg_match('/^\d+$/', $header[$j])) {
						if (!empty($currentTable[$i][$j])) {
							$currracerId = intval($header[$j]);
							$currLapTimeStr = $this->get_lap_time($currentTable[$i][$j]);
							$this->raceResult->$currracerId->lapData->addLapTime(floatval($currLapTimeStr));
						}
					}
				}
			}
		}
		return array();
	}

	function __construct($fileContent) {

		$this->fileContent = $fileContent;

		// new method, instead of analyzing the file with patterns, analyze it as table
		// each line is a row, and process the row for columns.
		// if there's an empty line, then finalize table content and ready to start new table.

		$currentTable = array();

		$realLine = "";
		$quoteCount = 0;

		foreach ($this->fileContent as $line) {
			$cleanLine = trim($line);

			if (empty($cleanLine)) {
				if (!empty($currentTable)) {
					$currentTable = $this->process_table($currentTable);
				}
			} else {
				// line not empty, collect data for table.
				$quoteCount += substr_count($line, "\"");

				$realLine .= $line;
				if ($quoteCount % 2 == 0) {
					$columns = explode("\t", $realLine);
					$currentTable[] = $columns;
					$realLine = "";
					$quoteCount = 0;

				}
			}
		}

		//var_dump($this->raceResult);

		if (!empty($currentTable)) {
			$currentTable = $this->process_table($currentTable);
		}
	}
} 