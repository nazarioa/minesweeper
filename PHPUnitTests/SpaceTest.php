<?php

use PHPUnit\Framework\TestCase;
use Minesweeper\Space;

require __DIR__ . '/../vendor/autoload.php';


class SpaceTest extends TestCase {
  public function testSetMineAndIsMine() {
    $space = new Space();
    $space->setMine( TRUE );
    $this->assertEquals( $space->isMine(), TRUE );
  }

  public function testSetTrippedAndTrippedTrue() {
    $space = new Space();
    $space->setTripped( TRUE );
    $this->assertEquals( $space->tripped(), TRUE );
  }

  public function testSetVolatilityAndVolatility() {
    $space          = new Space();
    $testVolatility = 4;
    $space->setVolatility( $testVolatility );
    $this->assertEquals( $space->volatility(), $testVolatility );
  }
}
