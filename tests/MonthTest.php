<?php
use Cake\I18n\Time;
use Rcnchris\Common\Month;
use Tests\Rcnchris\Common\BaseTestCase;

class MonthTest extends BaseTestCase
{
    public function makeMonth($month = null, $year = null)
    {
        return new Month($month, $year);
    }

    public function testInstance()
    {
        $this->ekoTitre('Common - Month');
        $today = new\DateTime();
        $month = $this->makeMonth();
        $this->assertInstanceOf(Month::class, $month);
        $this->assertEquals($today->format('m-Y'), $month->getFirstDay()->format('m-Y'));
    }

    public function testHelp()
    {
        $this->assertHasHelp($this->makeMonth());
    }

    public function testMagicToString()
    {
        $this->assertEquals('Janvier - 2019', (string)$this->makeMonth(1, 2019));
    }

    public function testGetFirstDay()
    {
        $firstDay = $this->makeMonth(1, 2019)->getFirstDay();
        $this->assertInstanceOf(Time::class, $firstDay);
        $this->assertEquals('01-01-2019', $firstDay->format('d-m-Y'));
    }

    public function testGetLastDay()
    {
        $lastDay = $this->makeMonth(1, 2019)->getLastDay();
        $this->assertInstanceOf(Time::class, $lastDay);
        $this->assertEquals('31-01-2019', $lastDay->format('d-m-Y'));
    }

    public function testGetWeeks()
    {
        $month = $this->makeMonth(1, 2019);
        $this->assertEquals(4, $month->getWeeks());
    }

    public function testWithInMonth()
    {
        $month = $this->makeMonth(1, 2019);
        $dateIn = (new \DateTime())->createFromFormat('d-m-Y', '15-01-2019');
        $this->assertTrue($month->inMonth($dateIn));
        $dateOut = (new \DateTime())->createFromFormat('d-m-Y', '15-02-2019');
        $this->assertFalse($month->inMonth($dateOut));
    }

    public function testNextMonth()
    {
        $month = $this->makeMonth(1, 2019);
        $next = $month->nextMonth();
        $this->assertInstanceOf(Month::class, $next);
        $this->assertEquals('Février - 2019', (string)$next);
    }

    public function testNextMonthInDecember()
    {
        $month = $this->makeMonth(12, 2018);
        $next = $month->nextMonth();
        $this->assertInstanceOf(Month::class, $next);
        $this->assertEquals('Janvier - 2019', (string)$next);
    }

    public function testPreviousMonth()
    {
        $month = $this->makeMonth(1, 2019);
        $previous = $month->previousMonth();
        $this->assertInstanceOf(Month::class, $previous);
        $this->assertEquals('Décembre - 2018', (string)$previous);
    }
}
