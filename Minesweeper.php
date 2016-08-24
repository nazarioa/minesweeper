<?php
class Minesweeper {

  /**
  * $map Array().
  * Holds Space objects that make up the Minesweeper map.
  */
  private $map = array();

  private $debug = false;

  /**
  * $gameOver boolean
  * Stores if the game has ended.
  */
  private $gameOver = false;

  /**
  * $width int.
  * Holds the width of the map.
  */
  private $width = null;

  /**
  * $height int.
  * Holds the height of the map.
  */
  private $height = null;

  /**
  * const C, H, V
  * These constants are used in the priniting of a map.
  *
  * C - Map corner.
  * H - Map horizontal edge.
  * V - Map verticle edge.
  */
  const C = '+';
  const H = '-';
  const V = '|';

  /**
  * $adjacents array const.
  * An array holding relative addresses of adacent cells.
  * This array is not altered during execution. It is used as short hand.
  */
  private $adjacents = array(
    'topleft' => ['x' => -1, 'y' => -1],
    'topmiddle' => ['x' => 0, 'y' => -1],
    'topright' => ['x' => 1, 'y' => -1],
    'left' => ['x' => -1, 'y' => 0],
    'right' => ['x' => 1, 'y' => 0],
    'bottomleft' => ['x' => -1, 'y' => 1],
    'bottommiddle' => ['x' => 0, 'y' => 1],
    'bottomright' => ['x' => 1, 'y' => 1],
  );

  /**
  * $loopcount int
  * Used for troubleshooting.
  */
  private static $loopcount = 0;


  /**
  * __constructor()
  * @param $width int - Width of minesweeper map
  * @param $height int - Height of minesweeper map
  * @param $mines array - Two demensional array with locations of mines.
  *
  * @return none;
  */
  public function __construct ($width, $height, array $mines, $options = null) {
    $this->game_init($width, $height, $mines, $options);
  }

  /**
  * game_init()
  * @param $width int - Width of minesweeper map
  * @param $height int - Height of minesweeper map
  * @param $mines array - Two demensional array with locations of mines.
  * Example:
  * $mines = [
  *  ['x' => 1, 'y' => 1],
  *  ['x' => 0, 'y' => 2],
  * ];
  * @return none;
  */
  public function game_init($width, $height, array $mines, $options = null){
    if ( is_array($options) ) {
      if ($options['debug'] === true) {
        $this->debug = true;
      }
    }

    try {
      $this->setWidth($width);
    } catch (Exception $e) {
      throw new Exception("Width invalid", 1);
    }

    try {
      $this->setHeight($height);
    } catch (Exception $e) {
      throw new Exception("Height invalid", 1);
    }

    try {
      $this->setMines ($mines);
    } catch (Exception $e) {
      throw new Exception("Third parameter should be an array of mines.", 1);
    }

    $this->buildMap();
    $this->printMap();
  }

  private function setWidth($width) {
    if ( !is_int($width) ) {
      throw new Exception("Not an integer", 1);
    }

    $this->width = $width;
  }

  private function width() {
    return $this->width;
  }

  private function setHeight($height) {
    if ( !is_int($height) ) {
      throw new Exception("Not an integer", 1);
    }

    $this->height = $height;
  }

  private function height() {
    return $this->height;
  }

  private function setMines ($mines) {
    if( !is_array ($mines) ) {
      throw new Exception("Not an array", 1);
    }

    $this->mines = $mines;
  }

  public function setDebug ($debug) {
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
  *
  * @param none.
  */
  public function clearMap () {
    for ($i = 0; $i < $this->width(); $i++) {
      $this->map[$i] = array();
      for ($j = 0; $j < $this->height(); $j++) {
        $space = new Space();
        $this->map[$i][$j] = $space;
        $space = new Space(array('debug' => $this->debug));
      }
    }
  }

  /**
  * placeMines()
  * Takes $mines array and stores sets Space objects to be mines
  * @param none.
  */
  public function placeMines () {
    foreach ($this->mines as $key => $entry) {
      $this->map[$entry['x']][$entry['y']]->setMine(true);
    }
  }

  /**
  * defuse($x, $y)
  * The method to call when a player wishes to try their luck at difusing a mine.
  * @param $x int
  * @param $y int
  */
  public function defuse($x, $y) {
    if($this->boundsCheck($x, $y) == false) {
      die('Out of bounds.');
    }

    try {
      $result = $this->map[$x][$y]->isMine();
    } catch (Exception $e) {
      throw new Exception("Error Processing Request: " . $x . $y, 1);
    }

    if ($result == true) {
      $this->printAnswer();
      $this->gameOver = true;
      echo PHP_EOL . 'You hit a mine.' . "\n" . 'Game over!' . PHP_EOL;
    } else {
      $this->map[$x][$y]->setTripped(true);
      $this->testAdjacentTo ($x, $y);
      echo self::$loopcount;
      $this->printMap();
    }
  }

  private function testAdjacentTo ($x, $y) {
    self::$loopcount = self::$loopcount + 1;

    if(self::$loopcount > 20) {
      die('Loop count is ' . self::$loopcount);
    }

    if ( $this->boundsCheck($x, $y) == true ) { // Are we within the bounds of the map?

      $volatility = $this->squareVolatility($x, $y); // Are we at a square next to a mine?
      $this->map[$x][$y]->setVolatility($volatility);

      echo PHP_EOL;
      echo ' Volaitlity for (' . $x .', ' . $y . ') is: ' . $volatility;

      if($volatility == 0) {
        $this->map[$x][$y]->setTripped(true); // change this sqaure to disarmed.

        foreach ($this->adjacents as $key => $position) {
          echo PHP_EOL;
          echo 'This is the next relative position. ' . $key . ' [' . $position['x'] . ', ' . $position['y'] . ']';
          echo PHP_EOL;
          echo 'This is the next absolut position. (' . ($x + $position['x']) . ', ' . ($y + $position['y']) . ')';
          $this->testAdjacentTo( ($x + $position['x']), ($y + $position['y']) );
        }

      } elseif ($volatility > 0) {
        echo PHP_EOL;
        return;
      }
    }

    echo PHP_EOL;
    echo PHP_EOL;
    return;
  }

  /*
  // if(self::$loopcount > 5) {
  //   die('Loop count is 100');
  // }
  //
  //
  // self::$loopcount = self::$loopcount + 1;
  //
  // echo self::$loopcount;
  // echo PHP_EOL;
  //
  // $keep_testing = true;
  //
  // if ( $this->boundsCheck($x, $y) == false ) { // Are we at the edge?
  //   self::$loopcount = 0;
  //   // $keep_testing = false;
  //   return $keep_testing; // If we are lets stop here, try the next sqaure.
  // }
  //
  // $volatility = $this->squareVolatility($x, $y); // Are we at a square next to a mine?
  // $this->map[$x][$y]->setVolatility($volatility);
  //
  // echo 'Testing (' . $x . ', ' . $y . ') ' . 'And it is: ' . $volatility;
  // echo PHP_EOL;
  //
  // if($volatility == 0) {
  //   $this->map[$x][$y]->setTripped(true);
  //
  //   foreach ($this->adjacents as $key => $position) {
  //     $result = $this->testAdjacentTo($x + $position[0], $y + $position[1]);
  //     if ($result == true) {
  //       return $keep_testing;
  //     }
  //   }
  // } elseif ($volatility > 0) {
  //   self::$loopcount = 0;
  //   $keep_testing = false;
  //   return $keep_testing;
  // }
  //
  // echo 'Strawberry';
  //
  // return $keep_testing;
  */

  private function squareVolatility($x, $y) {
    $volatility = 0;
    foreach ($this->adjacents as $key => $position) {
      $newX = $x - $position['x'];
      $newY = $y - $position['y'];
      if( $this->boundsCheck ( $newX, $newY ) == true ) {
        $isMine = $this->map[$newX][$newY]->isMine();
        if ($isMine == true) {
          $volatility++;
        }
      }
    }
    return $volatility;
  }

  public function printMap () {
    echo PHP_EOL . PHP_EOL;
    echo 'Prinitng Map';
    echo PHP_EOL;
    echo self::C;
    for ($i=0; $i < $this->width(); $i++) {
      echo self::H;
    }
    echo self::C;

    echo PHP_EOL;

    for ($j=0; $j < $this->height(); $j++) {
      echo self::V;
      for ($i=0; $i < $this->width(); $i++) {
        $volatility = $this->squareVolatility($i, $j);
        $this->map[$i][$j]->setVolatility($volatility);
        if($this->gameOver == true) {
          echo $this->map[$i][$j]->printSquare(true);
        } else {
          echo $this->map[$i][$j]->printSquare(false);
        }
      }
      echo self::V;
      echo PHP_EOL;
    }

    echo self::C;
    for ($i=0; $i < $this->width(); $i++) {
      echo self::H;
    }
    echo self::C;
  }

  private function printAnswer () {
    $this->gameOver = true;
    $this->printMap();
  }
  /**
  * boundsCheck($x, $y)
  * Checks to see if x, y coordiantes are within the map.
  * @param $x int
  * @param $y int
  * @return bool - True we are in bounds, False we are out of bounds.
  */
  private function boundsCheck($x, $y) {
    if ($x >= 0 && $x < $this->width() ) {
      if ( $y >= 0 && $y < $this->height() ) {
        return true;
      }
    }

    return false;
  }

}
