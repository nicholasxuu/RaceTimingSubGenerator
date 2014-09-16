<?php
namespace Model\Event;
use Model\Common\Globals;

/**
 * Class TotalResult
 * Total result for the whole race event.
 * @package Event
 */
class TotalResult {
	/** @var string */
	public $eventName;

	/**
	 * List of race results.
	 * @var RaceResult[]
	 */
	public $raceResultList = array();

	/**
	 * Default constructor.
	 */
	function __construct() {
		$this->raceResultList = array();
	}
	
	/**
	 * Add an empty race to the race list.
	 * @param string $name
	 * @return int
	 */
	function addRace($name) {
		$this->raceResultList[] = new RaceResult($name);
		return array_pop(array_keys($this->raceResultList));
	}

	/**
	 * @return array
	 */
	function getRaceNameList() {
		$retval = array();
		foreach ($this->raceResultList as $rid => $raceResult) {
			$retval[$rid] = $raceResult->name;
		}
		return $retval;
	}

	/**
	 * Get raceResultList index with exact race name.
	 * @param  string $name
	 * @return int
	 */
	function findRaceIdByName($name) {
		foreach ($this->raceResultList as $i => $rr) {
			if ($rr->name == $name) {
				return $i;
			}
		}
		return 0;
	}

	/**
	 * Search race result contain all elements in query.
	 * Query delimited by space character.
	 * Return array of indexes.
	 * @param string $query
	 * @return int[]
	 */
	function searchRaceByName($query) {
		$retval = array();
		foreach ($this->raceResultList as $i => $rr) {
			if (Globals::searchQuery($rr->name, $query)) {
				$retval[] = $i;
			}
		}
		return $retval;
	}
	
	/**
	 * Universal __get function.
	 * @param string $prop
	 * @return RaceResult
	 */
	function __get($prop) {
		if (is_numeric($prop)) {
			if (isset($this->raceResultList[intval($prop)])) {
				return $this->raceResultList[intval($prop)];
			}
		} else {
			foreach ($this->raceResultList as $rr) {
				if ($rr->name == $prop) {
					return $rr;
				}
			}
		}
		return NULL;
	}
	
	/**
	 * Universal __set function.
	 * @param string $prop
	 * @param RaceResult $value
	 */
	function __set($prop, $value) {
		if (is_string($prop)) {
			foreach ($this->raceResultList as $i => $rr) {
				if ($rr->name == $prop) {
					$this->raceResultList[$i] = $value;
				}
			}
		} else if (is_numeric($prop)) {
			$this->raceResultList[$prop] = $value;
		} 
	}
}