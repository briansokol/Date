<?php

require_once "../src/briansokol/Date/Date.php";
require_once "../src/briansokol/Date/Interval.php";

use briansokol\Date\Date;
use briansokol\Date\Interval;

class IntervalTest extends PHPUnit_Framework_TestCase {

	protected $interval;

	protected function setUp() {
		date_default_timezone_set("America/Phoenix");
		$this->interval = new Interval();
	}

	public function testConstructor() {
		$this->assertEquals('briansokol\Date\Interval', get_class($this->interval));
	}

	public function testSetDate1() {
		$this->interval->setDate1(
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00')
		);
		$this->assertEquals('briansokol\Date\Date', get_class($this->interval->getDate1()));
		$this->assertEquals('2015-04-01T13:43:21-07:00', $this->interval->getDate1()->format('c'));
	}

	public function testSetDate2() {
		$this->interval->setDate2(
			Date::getInstance()->setDateTimeFromString('2015-04-05T15:45:27-07:00')
		);
		$this->assertEquals('briansokol\Date\Date', get_class($this->interval->getDate2()));
		$this->assertEquals('2015-04-05T15:45:27-07:00', $this->interval->getDate2()->format('c'));
	}

	public function testSetDates() {
		$this->interval->setDates(
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00'),
			Date::getInstance()->setDateTimeFromString('2015-04-05T15:45:27-07:00')
		);
		$this->assertEquals('briansokol\Date\Date', get_class($this->interval->getDate1()));
		$this->assertEquals('briansokol\Date\Date', get_class($this->interval->getDate2()));
		$this->assertEquals('2015-04-01T13:43:21-07:00', $this->interval->getDate1()->format('c'));
		$this->assertEquals('2015-04-05T15:45:27-07:00', $this->interval->getDate2()->format('c'));
	}

	public function testGetIntervalObject() {
		$this->interval->setDates(
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00'),
			Date::getInstance()->setDateTimeFromString('2015-04-05T15:45:27-07:00')
		);
		$interval = $this->interval->getInterval();
		$this->assertEquals('DateInterval', get_class($interval));
	}

	public function testGetIntervalFormatted() {
		$this->interval->setDates(
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00'),
			Date::getInstance()->setDateTimeFromString('2015-04-05T15:45:27-07:00')
		);
		$this->assertEquals('2:02:06', $this->interval->getInterval('%h:%I:%S'));
		$this->assertEquals('4', $this->interval->getInterval('%d'));
	}

	public function testGetIntervalFormattedNegative() {
		$this->interval->setDates(
			Date::getInstance()->setDateTimeFromString('2015-04-05T15:45:27-07:00'),
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00')
		);
		$this->assertEquals('-2:02:06', $this->interval->getInterval('%r%h:%I:%S'));
		$this->assertEquals('-4', $this->interval->getInterval('%r%d'));
	}

	public function testGetIntervalFormattedAbsolute() {
		$this->interval->setDates(
			Date::getInstance()->setDateTimeFromString('2015-04-05T15:45:27-07:00'),
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00')
		);
		$this->interval->setAbsolute();
		$this->assertEquals('2:02:06', $this->interval->getInterval('%r%h:%I:%S'));
		$this->assertEquals('4', $this->interval->getInterval('%r%d'));
	}

	public function testSetInterval() {
		$this->interval->setDate1(
			Date::getInstance()->setDateTimeFromString('2015-04-01T13:43:21-07:00')
		);
		$this->interval->setInterval('4 days + 2 hours + 2 minutes + 6 seconds');
		$this->assertEquals('2015-04-05T15:45:27-07:00', $this->interval->getDate2()->format('c'));
	}
}
