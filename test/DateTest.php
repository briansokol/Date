<?php

require_once "../src/briansokol/Date/Date.php";

use briansokol\Date\Date;

class DateTest extends PHPUnit_Framework_TestCase {

	protected $date;

	protected function setUp() {
		date_default_timezone_set("America/Phoenix");
		$this->date = new Date();
	}

	public function testConstructor() {
		$this->assertEquals('briansokol\Date\Date', get_class($this->date));
	}

	public function testSetDateTime() {
	$this->date->setDate('2015', '04', '01');
	$this->assertEquals('2015-04-01', $this->date->format('Y-m-d'));

	$this->date->setTime('13', '43', '21');
	$this->assertEquals('13:43:21', $this->date->format('H:i:s'));

	$this->assertEquals('2015-04-01T13:43:21-07:00', $this->date->format());
}

	public function testSetDateTimeFromString() {
		$this->date->setDateTimeFromString('2015-04-01 13:43:21');
		$this->assertEquals('2015-04-01T13:43:21-07:00', $this->date->format());
	}

	public function testSetDateTimeFromStringWithTimezone() {
		$timezone = new \DateTimeZone('Pacific/Honolulu');
		$this->date->setDateTimeFromString('2015-04-01 13:43:21', $timezone);
		$this->assertEquals('2015-04-01T13:43:21-10:00', $this->date->format());
	}

	public function testSetTimestamp() {
		$this->date->setTimestamp(1427921001);
		$this->assertEquals('2015-04-01T13:43:21-07:00', $this->date->format());
	}

	public function testSetTimezone() {
		$timezone = new \DateTimeZone('Pacific/Honolulu');
		$this->date->setDateTimeFromString('2015-04-01 13:43:21');
		$this->date->setTimezone($timezone);
		$this->assertEquals('2015-04-01T10:43:21-10:00', $this->date->format());
	}

	public function testSetFormat() {
		$this->date->setDateTimeFromString('2015-04-01 13:43:21');
		$this->date->setFormat('Y-m-d H:i:s');
		$this->assertEquals('2015-04-01 13:43:21', $this->date->format());
	}

	public function setGetDate() {
		$this->assertEquals('\DateTime', get_class($this->date->getDate()));
	}

	public function testGetStartOfMonth() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-04-01T00:00:00-07:00', $this->date->getStartOfMonth());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2015-03-01T00:00:00-07:00', $this->date->getStartOfMonth());
	}

	public function testGetEndOfMonth() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-04-30T00:00:00-07:00', $this->date->getEndOfMonth());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2015-03-31T00:00:00-07:00', $this->date->getEndOfMonth());
	}

	public function testSetFirstMonthOfFirstQuarter() {
		$quarters = $this->date->getQuarters();
		$this->assertEquals(1, $quarters[0]);
		$this->assertEquals(4, $quarters[1]);
		$this->assertEquals(7, $quarters[2]);
		$this->assertEquals(10, $quarters[3]);

		$this->date->setFirstMonthOfFirstQuarter(2);
		$quarters = $this->date->getQuarters();
		$this->assertEquals(2, $quarters[0]);
		$this->assertEquals(5, $quarters[1]);
		$this->assertEquals(8, $quarters[2]);
		$this->assertEquals(11, $quarters[3]);

		$this->date->setFirstMonthOfFirstQuarter(9);
		$quarters = $this->date->getQuarters();
		$this->assertEquals(3, $quarters[0]);
		$this->assertEquals(6, $quarters[1]);
		$this->assertEquals(9, $quarters[2]);
		$this->assertEquals(12, $quarters[3]);
	}

	public function testGetStartOfQuarter() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-04-01T00:00:00-07:00', $this->date->getStartOfQuarter());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2015-01-01T00:00:00-07:00', $this->date->getStartOfQuarter());

		$this->date->setFirstMonthOfFirstQuarter(2);
		$this->date->setDateTimeFromString('2015-01-15 13:43:21');
		$this->assertEquals('2014-11-01T00:00:00-07:00', $this->date->getStartOfQuarter());
	}

	public function testGetStartOfNextQuarter() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-07-01T00:00:00-07:00', $this->date->getStartOfNextQuarter());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2015-04-01T00:00:00-07:00', $this->date->getStartOfNextQuarter());

		$this->date->setFirstMonthOfFirstQuarter(2);
		$this->date->setDateTimeFromString('2015-12-15 13:43:21');
		$this->assertEquals('2016-02-01T00:00:00-07:00', $this->date->getStartOfNextQuarter());
	}

	public function testGetStartOfLastQuarter() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-01-01T00:00:00-07:00', $this->date->getStartOfLastQuarter());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2014-10-01T00:00:00-07:00', $this->date->getStartOfLastQuarter());

		$this->date->setFirstMonthOfFirstQuarter(2);
		$this->date->setDateTimeFromString('2015-01-15 13:43:21');
		$this->assertEquals('2014-08-01T00:00:00-07:00', $this->date->getStartOfLastQuarter());
	}

	public function testGetEndOfQuarter() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-06-30T23:59:59-07:00', $this->date->getEndOfQuarter());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2015-03-31T23:59:59-07:00', $this->date->getEndOfQuarter());
	}

	public function testGetEndOfNextQuarter() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-09-30T23:59:59-07:00', $this->date->getEndOfNextQuarter());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2015-06-30T23:59:59-07:00', $this->date->getEndOfNextQuarter());
	}

	public function testGetEndOfLastQuarter() {
		$this->date->setDateTimeFromString('2015-04-15 13:43:21');
		$this->assertEquals('2015-03-31T23:59:59-07:00', $this->date->getEndOfLastQuarter());

		$this->date->setDateTimeFromString('2015-03-15 13:43:21');
		$this->assertEquals('2014-12-31T23:59:59-07:00', $this->date->getEndOfLastQuarter());
	}
}
