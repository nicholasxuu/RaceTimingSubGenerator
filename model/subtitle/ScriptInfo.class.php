<?php
namespace Model\Subtitle;

class ScriptInfo {
	public $playResX;
	public $playResY;

	/**
	 * Default constructor
	 */
	function __construct() {
		$this->playResX = 1920;
		$this->playResY = 1080;
	}

	/**
	 * Setup the video resolution for ass subtitle.
	 * @param int $x
	 * @param int $y
	 */
	function setPlayRes($x, $y) {
		$this->playResX = $x;
		$this->playResY = $y;
	}

	/**
	 * @return string
	 */
	function __toString() {
		return "[Script Info]
; This is a Sub Station Alpha v4 script.
; For Sub Station Alpha info and downloads,
; go to http://www.eswat.demon.co.uk/
Title: Neon Genesis Evangelion - Episode 26 (neutral Spanish)
Original Script: 
Script Updated By: version 2.8.01
ScriptType: v4.00
Collisions: Normal
PlayResX: {$this->playResX}
PlayResY: {$this->playResY}
PlayDepth: 0
Timer: 100,0000

[V4+ Styles]
Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding
Style: DefaultVCD,Segoe UI Mono,35,&H33FFFFFF,&H00B4FCFC,&H01000008,&H80000008,-1,0,0,0,100,100,0,0,1,1,0,9,5,5,5,0

[Events]
Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text
";
	}
}