<?php

namespace Minesweeper;

class Minesweeper {

  /**
   * $map Array().
   * Holds Space objects that make up the Minesweeper map.
   */
  private $map = array();

  private $debug = FALSE;

  /**
   * $gameOver boolean
   * Stores if the game has ended.
   */
  private $gameOver = FALSE;

  /**
   * $width int.
   * Holds the width of the map.
   */
  private $width = NULL;

  /**
   * $height int.
   * Holds the height of the map.
   */
  private $height = NULL;

  /**
   * const C, H, V
   * These constants are used in the printing of a map.
   *
   * C - Map corner.
   * H - Map horizontal edge.
   * V - Map vertical edge.
   */
  const C = '+';
  const H = '-';
  const V = '|';

  /**
   * $adjacents array const.
   * An array holding relative addresses of adjacent cells.
   * This array is not altered during execution. It is used as short hand.
   *
   * Note: It was written to be in clockwise direction starting from the
   * top left corner of a 3x3 square. Center square is omitted.
   */
  private $adjacents = array(
    'top_left'      => [ 'x' => - 1, 'y' => - 1 ],
    'top_middle'    => [ 'x' => 0, 'y' => - 1 ],
    'top_right'     => [ 'x' => 1, 'y' => - 1 ],
    'middle_right'  => [ 'x' => 1, 'y' => 0 ],
    'bottom_right'  => [ 'x' => 1, 'y' => 1 ],
    'bottom_middle' => [ 'x' => 0, 'y' => 1 ],
    'bottom_left'   => [ 'x' => - 1, 'y' => 1 ],
    'middle_left'   => [ 'x' => - 1, 'y' => 0 ],
  );

  /**
   * $loopCount int
   * Used for troubleshooting.
   */
  private static $loopCount = 0;


  /**
   * __constructor()
   *
   * @param $width int - Width of minesweeper map
   * @param $height int - Height of minesweeper map
   * @param $mines array - Two dimensional array with locations of mines.
   */
  public function __construct( $width, $height, array $mines, $options = NULL ) {
    $this->game_init( $width, $height, $mines, $options );
  }

  /**
   * game_init()
   *
   * @param $width int - Width of minesweeper map
   * @param $height int - Height of minesweeper map
   * @param $mines array - Two dimensional array with locations of mines.
   * Example:
   * $mines = [
   *  ['x' => 1, 'y' => 1],
   *  ['x' => 0, 'y' => 2],
   * ];
   */
  public function game_init( $width, $height, array $mines, $options = NULL ) {
    if ( is_array( $options ) ) {
      if ( $options['debug'] === TRUE ) {
        $this->debug = TRUE;
      }
    }

    try {
      $this->setWidth( $width );
    } catch ( Exception $e ) {
      throw new Exception( "Width invalid", 1 );
    }

    try {
      $this->setHeight( $height );
    } catch ( Exception $e ) {
      throw new Exception( "Height invalid", 1 );
    }

    try {
      $this->setMines( $mines );
    } catch ( Exception $e ) {
      throw new Exception( "Third parameter should be an array of mines.", 1 );
    }

    $this->buildMap();
    $this->printMap();
  }

  private function setWidth( $width ) {
    if ( ! is_int( $width ) ) {
      throw new Exception( "Not an integer", 1 );
    }

    $this->width = $width;
  }

  private function width() {
    return $this->width;
  }

  private function setHeight( $height ) {
    if ( ! is_int( $height ) ) {
      throw new Exception( "Not an integer", 1 );
    }

    $this->height = $height;
  }

  private function height() {
    return $this->height;
  }

  private function setMines( $mines ) {
    if ( ! is_array( $mines ) ) {
      throw new Exception( "Not an array", 1 );
    }

    $this->mines = $mines;
  }

  public function setDebug( $debug ) {
    $this->debug = $debug;
  }

  public function debug() {
    return $this->debug;
  }

  private function buildMap() {
    $this->clearMap();
    $this->placeMines();
  }

  /**
   * clearMap()
   * Goes over map and stores Space objects in each.
   */
  public function clearMap() {
    for ( $x = 0; $x < $this->height(); $x ++ ) {
      $this->map[ $x ] = array();
      for ( $y = 0; $y < $this->width(); $y ++ ) {
        $space                 = new Space( array( 'debug' => $this->debug ) );
        $this->map[ $x ][ $y ] = $space;
      }
    }
  }

  /**
   * placeMines()
   * Takes $mines array and stores sets Space objects to be mines
   */
  public function placeMines() {
    foreach ( $this->mines as $key => $entry ) {
      $this->map[ $entry['x'] ][ $entry['y'] ]->setMine( TRUE );
    }
  }

  /**
   * defuse($x, $y)
   * The method to call when a player wishes to try their luck at defusing a mine.
   *
   * @param $x int
   * @param $y int
   */
  public function defuse( $x, $y ) {
    if ( $this->gameOver == TRUE ) {
      return;
    }

    if ( $this->boundsCheck( $x, $y ) == FALSE ) {
      die( 'Out of bounds.' );
    }

    echo PHP_EOL . PHP_EOL;
    echo 'Defusing (' . $x . ', ' . $y . ')';
    echo PHP_EOL;

    try {
      $result = $this->map[ $x ][ $y ]->isMine();
    } catch ( Exception $e ) {
      throw new Exception( "Error Processing Request: " . $x . $y, 1 );
    }

    if ( $result == TRUE ) {
      $this->printAnswer();
      $this->gameOver = TRUE;
      echo PHP_EOL . 'You hit a mine.' . "\n" . 'Game over!';
      echo PHP_EOL;
      echo 'Number of recursions to solve ' . self::$loopCount;
    } else {
      $this->map[ $x ][ $y ]->setTripped( TRUE );
      $this->testAdjacentTo( $x, $y );
      $this->printMap();
    }
  }

  private function testAdjacentTo( $x, $y ) {
    self::$loopCount = self::$loopCount + 1;

    if ( $this->boundsCheck( $x, $y ) == TRUE ) { // Are we within the bounds of the map?

      $volatility = $this->squareVolatility( $x, $y ); // Are we at a square next to a mine?
      $this->map[ $x ][ $y ]->setVolatility( $volatility );

      if ( $volatility == 0 ) {
        $this->map[ $x ][ $y ]->setTripped( TRUE ); // change this square to disarmed.

        foreach ( $this->adjacents as $key => $position ) {

          if ( $this->debug == TRUE ) {
            echo PHP_EOL;
            echo PHP_EOL;
            echo 'The next relative position is: ' . $key . ' [' . $position['x'] . ', ' . $position['y'] . '],';
            echo PHP_EOL;
            echo 'which is an absolute position of: (' . ( $x + $position['x'] ) . ', ' . ( $y + $position['y'] ) . ')';
          }

          /*
          This next chunk of code is interesting.
          Assume that you are at square (m, n) and you know you have to move to
          square (m+1, n+1), before the move happens and we call this function
          recursively, lets test a few things.
          A) Is it a valid square (in bounds of map).
          B) Has it been tripped (have we been here).
          */

          if ( $this->boundsCheck( $x + $position['x'], $y + $position['y'] ) == TRUE ) { // A) is it in bounds?
            $tripped = $this->map[ $x + $position['x'] ][ $y + $position['y'] ]->tripped(); // B) have we been here?
            if ( $tripped == FALSE ) {
              $this->testAdjacentTo( ( $x + $position['x'] ), ( $y + $position['y'] ) );
            }
          }
        }

      } elseif ( $volatility > 0 ) {
        return;
      }
    }

    return;
  }

  /**
   * squareVolatility($x, $y)
   * Finds the "volatility" index (int) of (x, y) square by looking at the
   * adjacent squares to see if they are mines themselves.
   */
  private function squareVolatility( $x, $y ) {
    $volatility = 0;
    foreach ( $this->adjacents as $key => $position ) {
      $newX = $x - $position['x'];
      $newY = $y - $position['y'];
      if ( $this->boundsCheck( $newX, $newY ) == TRUE ) {
        $isMine = $this->map[ $newX ][ $newY ]->isMine();
        if ( $isMine == TRUE ) {
          $volatility ++;
        }
      }
    }

    return $volatility;
  }

  public function printMap() {
    echo self::C;
    for ( $y = 0; $y < $this->width(); $y ++ ) {
      echo self::H;
    }
    echo self::C;

    echo PHP_EOL;

    for ( $x = 0; $x < $this->height(); $x ++ ) {
      echo self::V;
      for ( $y = 0; $y < $this->width(); $y ++ ) {
        $volatility = $this->squareVolatility( $x, $y );
        $this->map[ $x ][ $y ]->setVolatility( $volatility );
        if ( $this->gameOver == TRUE ) {
          echo $this->map[ $x ][ $y ]->printSquare( TRUE );
        } else {
          echo $this->map[ $x ][ $y ]->printSquare( FALSE );
        }
      }
      echo self::V;
      echo PHP_EOL;
    }

    echo self::C;
    for ( $y = 0; $y < $this->width(); $y ++ ) {
      echo self::H;
    }
    echo self::C;
  }

  private function printAnswer() {
    $this->gameOver = TRUE;
    $this->printMap();
  }

  /**
   * boundsCheck($x, $y)
   * Checks to see if x, y coordinates are within the map.
   *
   * @param $x int
   * @param $y int
   *
   * @return bool - True we are in bounds, False we are out of bounds.
   */
  private function boundsCheck( $x, $y ) {
    if ( $x >= 0 && $x < $this->height() ) {
      if ( $y >= 0 && $y < $this->width() ) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
