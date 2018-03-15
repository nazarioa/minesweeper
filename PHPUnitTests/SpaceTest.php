<?php

use PHPUnit\Framework\TestCase;
use Minesweeper\Space;

require __DIR__ . '/../vendor/autoload.php';


class SpaceTest extends TestCase {

  /**
   * @test Minesweeper\Space
   */
  public function setMineAndIsMine() {
    $space = new Space();
    $space->setMine( TRUE );
    $this->assertEquals( $space->isMine(), TRUE );
  }

  /**
   * @test Minesweeper\Space
   */
  public function setTrippedAndTrippedTrue() {
    $space = new Space();
    $space->setTripped( TRUE );
    $this->assertEquals( $space->tripped(), TRUE );
  }

  /**
   * @test Minesweeper\Space
   */
  public function setVolatilityAndVolatility() {
    $space          = new Space();
    $testVolatility = 4;
    $space->setVolatility( $testVolatility );
    $this->assertEquals( $space->volatility(), $testVolatility );
  }
}
