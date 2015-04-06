<?php

namespace briansokol\Date;

use briansokol\Date\Exception\QuarterStartException;

class Date {

	protected $date;
	protected $format;
	protected $firstMonthOfFirstQuarter;
	protected $quarters;

	function __construct() {
		$this->date = new \DateTime();
		$this->format = "c";
		$this->firstMonthOfFirstQuarter = 1;
		$this->calculateQuarters();
	}

	public function setDate($year, $month, $day) {
		$this->date->setDate($year, $month, $day);
		return $this;
	}

	public function setDateFromString($date, $timezone = null) {
		$this->date = new \DateTime($date, $timezone);
		return $this;
	}

	public function setTime($hour, $minute, $second = 0) {
		$this->date->setTime($hour, $minute, $second);
		return $this;
	}

	public function setTimestamp($unixTimestamp) {
		$this->date->setTimestamp($unixTimestamp);
		return $this;
	}

	public function setTimezone($timezone) {
		$this->date->setTimezone($timezone);
		return $this;
	}

	public function setFormat($format) {
		$this->format = $format;
		return $this;
	}

	public function setFirstMonthOfFirstQuarter($month) {
		if (!is_integer($month)) {
			throw new QuarterStartException("Month must be an integer.");
		}
		if ($month > 12 || $month < 1) {
			throw new QuarterStartException("Month must be between 1 and 12");
		}
		$this->firstMonthOfFirstQuarter = (int)$month;
		$this->calculateQuarters();
	}

	public function getDate($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}
		return $this->date->format($format);
	}

	public function getFirstDayOfMonth($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}
		$date = new \DateTime($this->date->format("Y-m-01"));
		return $date->format($format);
	}

	public function getLastDayOfMonth($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}
		$date = new \DateTime($this->date->format("Y-m-t"));
		return $date->format($format);
	}

	public function getFirstDayOfQuarter($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}

		$firstMonth = $this->calculateFirstMonthOfQuarter();

		$firstDate = new \DateTime(
			$this->date->format("Y-".str_pad($firstMonth, 2, "0", STR_PAD_LEFT)."-01 00:00:00"),
			$this->date->getTimezone()
		);

		return $firstDate->format($format);
	}

	public function getLastDayOfQuarter($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}

		$firstMonth = $this->calculateFirstMonthOfQuarter();

		$index = array_search($firstMonth, $this->quarters);
		if (++$index == 4)
			$index = 0;

		$lastDate = new \DateTime(
			$this->date->format("Y-".str_pad($this->quarters[$index], 2, "0", STR_PAD_LEFT)."-01 23:59:59"),
			$this->date->getTimezone()
		);
		$lastDate->sub(new \DateInterval('P1D'));

		return $lastDate->format($format);
	}

	public function getQuarters() {
		return $this->quarters;
	}

	private function calculateFirstMonthOfQuarter() {
		$thisMonth = $this->date->format("n");
		$currentStart = 0;
		foreach ($this->quarters as $quarter) {
			if ($thisMonth >= $quarter) {
				$currentStart = $quarter;
			} else {
				break;
			}
		}
		return $currentStart;
	}

	private function calculateQuarters() {
		$this->quarters = array();
		$month = $this->firstMonthOfFirstQuarter;
		for ($i = 0; $i < 4; $i++) {
			$this->quarters[] = $month;
			$month += 3;
			if ($month > 12) {
				$month -= 12;
			}
		}
		sort($this->quarters);
	}

	static public function getInstance() {
		return new static();
	}
}