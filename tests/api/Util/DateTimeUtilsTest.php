<?php

use Directus\Util\DateTimeUtils;

class DateTimeUtilsTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        ini_set('date.timezone', 'CET');
    }

    public function testConvertUtcDateTimezone()
    {
        $utcTime = '2016-06-28 16:13:18';
        $currentTimezone = 'CET';
        $dateTime = DateTimeUtils::createFromDefaultFormat($utcTime, 'UTC');
        $dateTime->switchToTimeZone($currentTimezone);
        $this->assertEquals('2016-06-28 17:13:18', $dateTime->toString('Y-m-d H:i:s'));
    }

    public function testNow()
    {
        $datetime = DateTimeUtils::now();
        $this->assertInstanceOf(DateTimeUtils::class, $datetime);
    }

    public function testUtc()
    {
        $datetime = DateTimeUtils::nowInUTC();
        $this->assertInstanceOf(DateTimeUtils::class, $datetime);
        $this->assertSame('UTC', $datetime->getTimezone()->getName());
    }

    public function testInDays()
    {
        $today = DateTimeUtils::now();
        $tomorrow = DateTimeUtils::inDays(1);

        $this->assertTrue($today < $tomorrow);
    }

    public function testDaysAgo()
    {
        $today = DateTimeUtils::now();
        $yesterday = DateTimeUtils::wasDays(1);

        $this->assertTrue($today > $yesterday);
    }

    public function testToGmt()
    {
        $current = DateTimeUtils::createFromDefaultFormat('2018-04-09 12:00:00');
        $gmt = $current->toGMT();

        $this->assertSame('GMT', $gmt->getTimezone()->getName());
        $gmtValue = '2018-04-09 16:00:00';
        $this->assertSame($gmtValue, $gmt->toString());
        $this->assertSame($gmtValue, $current->toGMTString());
    }

    public function testIso8601Format()
    {
        $current = DateTimeUtils::createFromDefaultFormat('2018-04-09 12:00:00', 'UTC');
        $isoFormat = $current->toISO8601Format();
        $this->assertSame('2018-04-09T12:00:00+00:00', $isoFormat);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidTimeZone()
    {
        DateTimeUtils::now('invalid-timezone');
    }
}
