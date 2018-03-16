<?php
/**
 * User: nazario
 * Date: 3/15/18
 */

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

  /**
   * @test Minesweeper\Space::printSquare
   */
  public function whenNoAnswerIsSuppliedAndNotTripped() {
    $space = new Space();
    $space->setTripped( FALSE );

    $this->expectOutputString(Space::HIDDEN);
    echo $space->printSquare( FALSE );
  }

  /**
   * @test Minesweeper\Space::printSquare
   */
  public function whenNoAnswerIsSuppliedAndIsTrippedAndVolatilityIsZero() {
    $space = new Space();
    $space->setTripped( TRUE );

    $this->expectOutputString(Space::SAFE);
    echo $space->printSquare( FALSE );
  }

  /**
   * @test Minesweeper\Space::printSquare
   */
  public function whenNoAnswerIsSuppliedAndIsTrippedAndVolatilityIsNotZero() {
    $space = new Space();
    $testVolatilityValue = 3;
    $space->setTripped( TRUE );
    $space->setVolatility($testVolatilityValue);

    $this->expectOutputString('' . $testVolatilityValue);
    echo $space->printSquare( FALSE );
  }
}
