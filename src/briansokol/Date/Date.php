<?php

namespace briansokol\Date;

use briansokol\Date\Exception\QuarterStartException;

/**
 * Class Date
 * @package briansokol\Date
 *
 * @author Brian Sokol <bri@nsokol.net>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @copyright 2015 Brian Sokol
 */
class Date {

	/**
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @var string
	 */
	protected $format;

	/**
	 * @var int
	 */
	protected $firstMonthOfFirstQuarter;

	/**
	 * @var int[]
	 */
	protected $quarters;

	/**
	 * Creates a new working \DateTime and sets the default format.
	 * Calculates the default quarter start months.
	 */
	function __construct() {
		$this->date = new \DateTime();
		$this->format = "c";
		$this->firstMonthOfFirstQuarter = 1;
		$this->calculateQuarters();
	}

	/**
	 * Sets the date portion from the year, day, and month.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @return \briansokol\Date\Date $this
	 */
	public function setDate($year, $month, $day) {
		$this->date->setDate($year, $month, $day);
		return $this;
	}

	/**
	 * Sets the date and time from a string.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param string $date
	 * @param \DateTimezone|null $timezone
	 * @return \briansokol\Date\Date $this
	 */
	public function setDateTimeFromString($date, $timezone = null) {
		$this->date = new \DateTime($date, $timezone);
		return $this;
	}

	/**
	 * Sets the time portion from the year, day, and month.
	 *
	 * @param int $hour
	 * @param int $minute
	 * @param int $second
	 * @return \briansokol\Date\Date $this
	 */
	public function setTime($hour, $minute, $second = 0) {
		$this->date->setTime($hour, $minute, $second);
		return $this;
	}

	/**
	 * Sets the date and time from a Unix timestamp.
	 *
	 * @param int $unixTimestamp
	 * @return \briansokol\Date\Date $this
	 */
	public function setTimestamp($unixTimestamp) {
		$this->date->setTimestamp($unixTimestamp);
		return $this;
	}

	/**
	 * Sets the timezone.
	 * It is recommended that the timezone always be explicitly set using this function
	 * or passing it as a parameter to other functions.
	 * Otherwise, be sure to set a default timezone using the date.timezone setting or
	 * calling date_default_timezone_set().
	 *
	 * @param \DateTimezone $timezone
	 * @return \briansokol\Date\Date $this
	 */
	public function setTimezone($timezone) {
		$this->date->setTimezone($timezone);
		return $this;
	}

	/**
	 * Sets the format used when retrieving the date as a string.
	 * This default format will be used is one is not supplied.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param string $format
	 * @return \briansokol\Date\Date $this
	 */
	public function setFormat($format) {
		$this->format = $format;
		return $this;
	}

	/**
	 * Sets the first month of the first quarter of the year.
	 * This will be used to calculate all 4 quarters.
	 *
	 * @param int $month The month number of the first month of the quarter.
	 * @return \briansokol\Date\Date $this
	 * @throws QuarterStartException if the provided month is out of range.
	 */
	public function setFirstMonthOfFirstQuarter($month) {
		if (!is_integer($month)) {
			throw new QuarterStartException("Month must be an integer.");
		}
		if ($month > 12 || $month < 1) {
			throw new QuarterStartException("Month must be between 1 and 12");
		}
		$this->firstMonthOfFirstQuarter = (int)$month;
		$this->calculateQuarters();

		return $this;
	}

	/**
	 * Returns the date as a formatted string.
	 * If no format is provided, the default format will be used.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param string|null $format
	 * @return string
	 */
	public function getDate($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}
		return $this->date->format($format);
	}

	/**
	 * Returns the start of the month.
	 * If no format is provided, the default format will be used.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param string|null $format
	 * @return string
	 */
	public function getStartOfMonth($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}
		$date = new \DateTime($this->date->format("Y-m-01"));
		return $date->format($format);
	}

	/**
	 * Returns the end of the month.
	 * If no format is provided, the default format will be used.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param null $format
	 * @return string
	 */
	public function getEndOfMonth($format = null) {
		if (empty($format)) {
			$format = $this->format;
		}
		$date = new \DateTime($this->date->format("Y-m-t"));
		return $date->format($format);
	}

	/**
	 * Returns the start of the quarter.
	 * If no format is provided, the default format will be used.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param null $format
	 * @return string
	 */
	public function getStartOfQuarter($format = null) {
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

	/**
	 * Returns the end of the quarter.
	 * If no format is provided, the default format will be used.
	 * See {@link http://php.net/manual/en/function.date.php} for format options.
	 *
	 * @param null $format
	 * @return string
	 */
	public function getEndOfQuarter($format = null) {
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

	/**
	 * Returns an array consisting of the months numbers beginning each quarter of the year.
	 *
	 * @return int[]
	 */
	public function getQuarters() {
		return $this->quarters;
	}

	/**
	 * Determines the number of the month beginning this quarter
	 *
	 * @return int
	 */
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

	/**
	 * Calculates the month numbers beginning each quarter.
	 */
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

	/**
	 * @return static
	 */
	static public function getInstance() {
		return new static();
	}
}