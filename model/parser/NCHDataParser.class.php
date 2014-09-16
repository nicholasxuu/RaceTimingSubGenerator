<?php
/**
 * parse NCH formatted result data file into RaceResult object
 * 
 * @author Nicholas Xu
 *
 */


namespace Model\Parser;
use Model\Event;

class NCHDataParser
{
	/** @var Event\TotalResult */
	public $totalResult;
	
	/** @var string[] */
	public $fileContent;

	/**
	 * Get array of car# based on driver section's headerline.
	 * @param string $line
	 * @return array
	 */
	function get_driver_section_header_array($line) {
		$driSectLine = trim($line);
		$driSectLine = trim($driSectLine, "_");
		$driSectLine = str_replace(" ", "", $driSectLine);
		$driSectArr = preg_split('/_+/', $driSectLine);
		return $driSectArr;
	}

	/**
	 * Get array of car# based on lap section's headerline.
	 * @param string $line
	 * @return array
	 */
	function get_lap_section_header_array($line) {
		$lapSectLine = trim($line);
		$lapSectLine = trim($lapSectLine, "_");
		$lapSectLine = str_replace(" ", "", $lapSectLine);
		$lapSectArr = preg_split('/_+/', $lapSectLine);
		return $lapSectArr;
	}

	/**
	 * Get the index of delimiters in a line of lap section headerline,
	 * Later laptime lines will be in the same index format.
	 * @param string $line
	 * @param string $delimiter
	 * @return array
	 */
	function get_lap_section_index($line, $delimiter = " ") {
		$last_i = 0;
		$lapSectIndex = array();
		while (($i = strpos($line, $delimiter, $last_i)) !== false) {
			$last_i = $i + 1;
			array_push($lapSectIndex, $i);
		}
		return $lapSectIndex;
	}

	/**
	 * Convert column name in data to field name in event data.
	 * @param string $input
	 * @return bool
	 */
	function getRaceDriverDataMapping($input) {
		$raceDriverDataMapping = array(
			"Driver" 	=> "name",
			"FastLap" 	=> "bestLap",
			"Laps" 		=> "totalLaps",
			"RaceTime" 	=> "totalTime",
			"Car#" 		=> "carNum",
			"Behind" 	=> "behind",
		);
		
		if (isset($raceDriverDataMapping[$input])) {
			return $raceDriverDataMapping[$input];
		} else {
			return false;
		}
	}

	/**
	 * Default constructor
	 * @param string[] $fileContent
	 */
	function __construct($fileContent) {
		
		$this->fileContent = $fileContent;
		$this->totalResult = new Event\TotalResult();

		$section = 0;
		$driSectArr = array();
		$lapSectArr = array();
		$lapSectIndex = array(); 
		$type2input = false;

		$currRaceId = 0;
		$finish_position = 0; // set variable
		
		// process input file content
		foreach ($fileContent as $line)
		{
			if (trim($line) != "" || $section === 2)
			{
				$originalLine = $line;
				$line = trim($line);
				if (strstr($line, "Round#") !== false && strstr($line, "Race#") !== false) // title
				{
					// start new race
					$section = 0;
					
					$race_name_line_arr = preg_split("/[\s]{5,}/", $line);
					
					$currRaceName = trim($race_name_line_arr[0]);
					//$total_data[$curr_race] = array();
					$currRaceId = (string) $this->totalResult->addRace($currRaceName);
				}
				else if (preg_match('/.*Driver.*Car#.*Laps.*/', $line)) 
				{
					
					$section = 1;
					
					$driSectArr = $this->get_driver_section_header_array($line);
					
					$finish_position = 1;
					
				}
				else if (preg_match('/.*1\_.+2\_.+3\_.*/', $line))
				{
					$section = 2;

					$lapSectArr = $this->get_lap_section_header_array($originalLine);
					
					$lapSectIndex = $this->get_lap_section_index($originalLine);
				}
				else if ($section === 2 && preg_match('/----/', $line))
				{
					$section = 3;
				}
				else if ($section === 1)
				{
					$elementArr = preg_split('/\s+/', $line);
					$i = 1;
					$name = true;
					$currData = new Event\RaceDriverData();
					
					$driverName = "";
					foreach ($elementArr as $e)
					{
						// Assuming all start with driver's name
						if ($name && (! preg_match('/#\d/', $e)))
						{
							$driverName .= " " . $e;
						}
						else
						{
							$name = false;
							
							$mapped = $this->getRaceDriverDataMapping($driSectArr[$i]);
							if (!empty($mapped)) {
								$currData->$mapped = $e;
							}
							
							$i++;
						}	
					}
					
					$currData->name = trim($driverName);
					$currData->carNum = str_replace("#", "", $currData->carNum);
					$currData->finishPosition = $finish_position;
					$finish_position++;
					$this->totalResult->$currRaceId->addDriver($currData);
				}
				else if ($section === 2)
				{
					if (empty($line)) {
						$type2input = true;
					}
					else
					{
						$first_ending_splitter_index = 1; // first delimiter is in the beginning of the line, so we start from 1. this index is fixed for $lapSectArr
						for ($i = $first_ending_splitter_index; $i < count($lapSectIndex); $i++) {

							$plap_section = substr($originalLine, $lapSectIndex[$i-1], $lapSectIndex[$i] - $lapSectIndex[$i-1]);
							$plap_section = trim($plap_section);

							if (!empty($plap_section)) {
								$plap_section = explode("/", $plap_section);
								//var_dump($plap_section);
								$this->totalResult->raceResultList[$currRaceId]->$lapSectArr[$i-1]->lapData->addLapTime(floatval($plap_section[1]));
							}
						}
					}
				}
				else if ($section === 3)
				{
					// when new race starts, finish last race first
					if ($type2input) { // if it's second type of input, need to remove un-needed lines of data
						foreach ($lapSectArr as $carNum) {
							if (!is_null($this->totalResult->$currRaceId->$carNum)) {
								$this->totalResult->$currRaceId->$carNum->lapData->removeEveryOtherLapTime();
							}
						}
					}

					$section = 4; // 4 means end of one race
				}
			}
			
		}
		
		foreach($this->totalResult->raceResultList as $currRaceId => $result) {
			$this->totalResult->raceResultList[$currRaceId]->cleanUpDriver();
		}

		unset($this->fileContent);
	}
}