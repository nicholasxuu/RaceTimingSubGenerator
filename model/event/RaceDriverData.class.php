<?php
namespace Model\Event;

class RaceDriverData {
	/** @var string */
	public $name = "";

	/** @var string */
	public $nickname = "";

	/** @var string */
	public $bestLap; // one recorded in data sheet

	/** @var string */
	public $totalLaps;

	/** @var string */
	public $totalTime;

	/** @var string */
	public $carNum;

	/** @var string */
	public $behind;

	/** @var int */
	public $finishPosition;

	/** @var string */
	public $averageLap;

	/** @var RaceDriverLapResult */
	public $lapData;

	/**
	 * Default construtor.
	 */
	function __construct() {
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
	}
	
}
