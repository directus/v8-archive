<?php

use Directus\Util\DateTimeUtils;

class DateTimeUtilsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!ini_get('date.timezone')) {
            ini_set('date.timezone', 'America/New_York');
        }
    }

    public function testConvertUTCDateTimezone()
    {
        $utcTime = '2016-06-28 16:13:18';
        $currentTimezone = 'America/New_York';
        $dateTime = DateTimeUtils::createFromDefaultFormat($utcTime, 'UTC');
        $dateTime->switchToTimeZone($currentTimezone);
        $this->assertEquals('2016-06-28 12:13:18', $dateTime->toString('Y-m-d H:i:s'));
    }

    public function testNow()
    {
        $datetime = DateTimeUtils::now();
        $this->assertInstanceOf(DateTimeUtils::class, $datetime);
    }
}
