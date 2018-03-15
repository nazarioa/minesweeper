<?php
use PHPUnit\Framework\TestCase;
use Minesweeper\Space;

require __DIR__ . '/../vendor/autoload.php';


class SpaceTest extends TestCase
{
    public function testSetMineAndIsMine()
    {
        $space = new Space();
        $space->setMine(true);
        $this->assertEquals($space->isMine(), true);
    }

    public function testSetTrippedAndTrippedTrue()
    {
        $space = new Space();
        $space->setTripped(true);
        $this->assertEquals($space->tripped(), true);
    }

    public function testSetVolatilityAndVolatility()
    {
        $space = new Space();
        $testVolatility = 4;
        $space->setVolatility($testVolatility);
        $this->assertEquals($space->volatility(), $testVolatility);
    }
}
