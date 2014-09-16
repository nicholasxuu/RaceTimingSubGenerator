<?php
namespace Model\Event;
use Model\Common\Globals;

/**
 * Class RaceResult
 * @package Event
 * Represent a single race result data.
 */
class RaceResult {
	
	/**
	 * Race name.
	 * @var string
	 */
	public $name;
	
	/**
	 * Each race driver's data.
	 * @var RaceDriverData[]
	 */
	public $driverList;

	function __construct($name) {
		$this->name = $name;
		$this->driverList = array();
	}

	/**
	 * Universal __get function.
	 * @param string $prop
	 * @return RaceDriverData|string|array
	 */	
	function __get($prop) {
		if (isset($this->$prop)) {
			return $this->$prop;	
		}
		if (is_numeric($prop)) {
			foreach ($this->driverList as $driver) {
				if (intval($driver->carNum) == intval($prop)) {
					return $driver;
				}
			}
		}
		return NULL;
	}
	
	/**
	 * Add a new driver's data, return added data's index.
	 * @param RaceDriverData $rdd
	 * @return int
	 */
	function addDriver(RaceDriverData $rdd) {
		$this->driverList[] = $rdd;
		return array_pop(array_keys($this->driverList));
	}

	/**
	 * Remove driver's data from race if it has no racing lap data.
	 */
	function cleanUpDriver() {
		foreach ($this->driverList as $i => $rdd) {
			if ($rdd->lapData->isEmpty()) {
				unset($this->driverList[$i]);
			}
		}
		$this->driverList = array_values($this->driverList); 
	}
	
	/**
	 * Get driverList index by car number.
	 * @param string $num
	 * @return int
	 */
	function getIdByCarNum($num) {
		foreach ($this->driverList as $i => $rdd) {
			if ($rdd->carNum == $num) {
				return $i;
			}
		}
		return 0;
	}
	
	/**
	 * Get driverList index by driver name.
	 * @param string $name
	 * @return int
	 */
	function getIdByName($name) {
		foreach ($this->driverList as $i => $rdd) {
			if ($rdd->name == $name) {
				return $i;
			}
		}
		return -1;
	}

	function searchIdByName($query) {
		$retval = array();
		foreach ($this->driverList as $i => $rdd) {
			if (Globals::searchQuery($rdd->name, $query)) {
				$retval[] = $i;
			}
		}
		return $retval;
	}

	function getNameList() {
		$retval = array();
		foreach ($this->driverList as $i => $rdd) {
			$retval[$i] = $rdd->name;
		}
		return $retval;
	}

	/**
	 * used for sorting currTime
	 * 
	 * @param array $a
	 * @param array $b
	 * @return bool;
	 */
	function cmpCurrTime($a, $b) {
		return $a["currTime"] > $b["currTime"];
	}
	
	/**
	 * Get an array of float timestamp for any driver's lap change, sorted by the changing time.
	 * @return array
	 */
	function getTotalCurrTime() {
		$retval = array();
	
		foreach ($this->driverList as $driverId => $driverData) {
			$currTime = 0;
			foreach ($driverData->lapData->lapTimeList as $i => $laptime) {
				$currTime += $laptime;
				
				$currObj = array(
					"currTime" => $currTime,
					"driverId" => $driverId,
					"lap_i" => $i,
				);
				array_push($retval, $currObj);
			}
		}
		usort($retval, array('Model\Event\RaceResult', "cmpCurrTime"));
		
		return $retval;
	}
	
	
}

