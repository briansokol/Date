<?php

namespace briansokol\Date;

/**
 * Class Interval
 * @package briansokol\Date
 *
 * @author Brian Sokol <bri@nsokol.net>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @copyright 2015 Brian Sokol
 */
class Interval {

	/**
	 * @var Date
	 */
	protected $date1;

	/**
	 * @var Date
	 */
	protected $date2;

	/**
	 * @var \DateInterval
	 */
	protected $interval;

	/**
	 * @var bool
	 */
	protected $absolute;

	/**
	 * Creates default start and end dates, both are right now.
	 * Set absolute calculations to false.
	 */
	function __construct() {
		$this->date1 = new Date();
		$this->date2 = new Date();
		$this->absolute = false;
	}

	/**
	 * Sets both dates.
	 *
	 * @param \briansokol\Date\Date $date1
	 * @param \briansokol\Date\Date $date2
	 * @return \briansokol\Date\Interval $this
	 * @throws \briansokol\Date\Exception\InvalidDateException
	 */
	public function setDates($date1, $date2) {
		if (get_class($date1) != 'briansokol\Date\Date' || get_class($date2) != 'briansokol\Date\Date') {
			throw new Exception\InvalidDateException("Date must be an object");
		}
		$this->date1 = $date1;
		$this->date2 = $date2;
		$this->buildInterval();

		return $this;
	}

	/**
	 * Sets date 1.
	 *
	 * @param \briansokol\Date\Date $date
	 * @return \briansokol\Date\Interval $this
	 * @throws \briansokol\Date\Exception\InvalidDateException
	 */
	public function setDate1($date) {
		if (get_class($date) != 'briansokol\Date\Date') {
			throw new Exception\InvalidDateException("Date must be an object");
		}
		$this->date1 = $date;
		$this->buildInterval();
		return $this;
	}

	/**
	 * Sets date 2.
	 *
	 * @param \briansokol\Date\Date $date
	 * @return \briansokol\Date\Interval $this
	 * @throws \briansokol\Date\Exception\InvalidDateException
	 */
	public function setDate2($date) {
		if (get_class($date) != 'briansokol\Date\Date') {
			throw new Exception\InvalidDateException("Date must be an object");
		}
		$this->date2 = $date;
		$this->buildInterval();
		return $this;
	}

	/**
	 * If true, all interval values will be positive.
	 *
	 * @param bool $absolute
	 * @return \briansokol\Date\Interval $this
	 */
	public function setAbsolute($absolute = true) {
		$this->absolute = $absolute;
		$this->buildInterval();
		return $this;
	}

	/**
	 * Returns the first underlying \briansokol\Date\Date object.
	 *
	 * @return \briansokol\Date\Date
	 */
	public function getDate1() {
		return $this->date1;
	}

	/**
	 * Returns the first underlying \briansokol\Date\Date object.
	 *
	 * @return \briansokol\Date\Date
	 */
	public function getDate2() {
		return $this->date2;
	}

	/**
	 * Calculates date 2 from date 1 using the given interval string.
	 * The interval string can be in either strtotime() or ISO 8601 format.
	 * See {@link http://php.net/manual/en/function.strtotime.php} for strtotime() format options.
	 * See {@link http://php.net/manual/en/dateinterval.construct.php} for ISO 8601 format options.
	 *
	 * @param string $intervalString
	 * @return \briansokol\Date\Interval $this
	 * @throws \briansokol\Date\Exception\DateIntervalException
	 */
	public function setInterval($intervalString) {
		if (substr($intervalString,0,1) == 'P') {
			$interval = new \DateInterval($intervalString);
		} else {
			$interval = \DateInterval::createFromDateString($intervalString);
		}
		$this->date2 = clone $this->date1;
		$this->date2->getDate()->add($interval);
		$this->buildInterval();
		return $this;
	}

	/**
	 * If $format is false, returns a \DateInterval object containing the diff of both dates.
	 * If $format is a string, the formatted string interval is returned.
	 * See {@link http://php.net/manual/en/dateinterval.format.php} for format options.
	 *
	 * @param string|null $format
	 * @return \DateInterval|string
	 */
	public function getInterval($format = null) {
		if (empty($this->interval)) {
			$this->buildInterval();
		}
		if (empty($format)) {
			return $this->interval;
		}
		return $this->interval->format($format);
	}

	/**
	 * Creates a \DateInterval object from the given dates.
	 * If the \DateInterval object reports an error, an exception will be thrown.
	 *
	 * @throws \briansokol\Date\Exception\DateIntervalException
	 */
	private function buildInterval() {
		$this->interval = $this->date1->getDate()->diff($this->date2->getDate(), $this->absolute);
		if ($this->interval === false) {
			throw new Exception\DateIntervalException("Error calculating interval");
		}
	}

	/**
	 * Gets an instance of a \briansokol\Date\Interval object
	 *
	 * @return static
	 */
	static public function getInstance() {
		return new static();
	}
}