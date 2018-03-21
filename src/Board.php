<?php

namespace Minesweeper;

use Exception;

class Board
{

    /**
     * $map Array().
     * Holds Space objects that make up the Minesweeper map.
     */
    private $boardMap = array();

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
     * These constants are used in the printing of a map.
     *
     * C - Map corner.
     * H - Map horizontal edge.
     * V - Map vertical edge.
     */
    const MAP_CORNER = '+';
    const MAP_EDGE_TOPS = '-';
    const MAP_EDGE_SIDE = '|';

    /**
     * $adjacents array const.
     * An array holding relative addresses of adjacent cells.
     * This array is not altered during execution. It is used as short hand.
     *
     * Note: It was written to be in clockwise direction starting from the
     * top left corner of a 3x3 square. Center square is omitted.
     */
    private $adjacents = array(
        'top_left' => ['x' => -1, 'y' => -1],
        'top_middle' => ['x' => 0, 'y' => -1],
        'top_right' => ['x' => 1, 'y' => -1],
        'middle_right' => ['x' => 1, 'y' => 0],
        'bottom_right' => ['x' => 1, 'y' => 1],
        'bottom_middle' => ['x' => 0, 'y' => 1],
        'bottom_left' => ['x' => -1, 'y' => 1],
        'middle_left' => ['x' => -1, 'y' => 0],
    );

    /**
     * $loopCount int
     * Used for troubleshooting.
     */
    private static $loopCount = 0;


    /**
     * Create board in one of two ways:
     * - Board::game_init() where all aspects of the game must be defined by the user.
     * - Board::random() where the mines are randomly placed given a width, height, and difficulty.
     *
     * @param $width int - Width of minesweeper map
     * @param $height int - Height of minesweeper map
     * @param array $mines - Two dimensional array with locations of mines.
     * Example:
     * $mines = [
     *  ['x' => 1, 'y' => 1],
     *  ['x' => 0, 'y' => 2],
     * ];
     *
     * @throws \Exception
     */
    private function __construct($width, $height, array $mines, array $options = array())
    {
        if ($options['debug'] === true) {
            $this->debug = true;
        }

        try {
            $this->setWidth($width);
        } catch (Exception $e) {
            throw new Exception("Width invalid");
        }

        try {
            $this->setHeight($height);
        } catch (Exception $e) {
            throw new Exception("Height invalid");
        }

        try {
            $this->setMines($mines);
        } catch (Exception $e) {
            throw new Exception("Third parameter should be an array of mines.");
        }

        $this->buildMap();
        $this->printMap();
    }

    /**
     * @param $width
     * @param $height
     * @param array $mines
     * @param array $options
     *
     * @return \Minesweeper\Board
     * @throws \Exception
     */
    public static function game_init($width, $height, array $mines, array $options = array())
    {
        try {
            $board = new Board($width, $height, $mines, $options);
        } catch (Exception $e) {
            throw new Exception('Could not setup board. ' . $e->getMessage());
        }

        return $board;
    }

    /**
     * Todo:
     *
     * @param $width
     * @param $height
     * @param int $difficulty
     *
     * @return \Minesweeper\Board
     * @throws \Exception
     */
    public static function random($width, $height, $difficulty = 1)
    {
        $board = new Board($width, $height, array(), array());
        return $board;
    }

    /**
     * setWidth
     *
     * @param $width
     *
     * @throws \Exception
     */
    private function setWidth($width)
    {
        if (!is_int($width)) {
            throw new Exception("Not an integer", 1);
        }

        $this->width = $width;
    }

    private function width()
    {
        return $this->width;
    }

    /**
     * setHeight
     *
     * @param $height
     *
     * @throws \Exception
     */
    private function setHeight($height)
    {
        if (!is_int($height)) {
            throw new Exception("Not an integer", 1);
        }

        $this->height = $height;
    }

    private function height()
    {
        return $this->height;
    }

    /**
     * setMines
     *
     * @param array $mines
     *
     * @throws \Exception
     */
    private function setMines(array $mines)
    {
        if (!is_array($mines)) {
            throw new Exception("Not an array", 1);
        }

        $this->mines = $mines;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function debug()
    {
        return $this->debug;
    }

    private function buildMap()
    {
        $this->clearMap();
        $this->placeMines();
    }

    /**
     * clearMap()
     * Goes over map and stores Space objects in each.
     */
    public function clearMap()
    {
        for ($x = 0; $x < $this->height(); $x++) {
            $this->boardMap[$x] = array();
            for ($y = 0; $y < $this->width(); $y++) {
                $space = new Space(array('debug' => $this->debug));
                $this->boardMap[$x][$y] = $space;
            }
        }
    }

    /**
     * placeMines()
     * Takes $mines array and stores sets Space objects to be mines
     */
    public function placeMines()
    {
        foreach ($this->mines as $key => $entry) {
            $this->boardMap[$entry['x']][$entry['y']]->setMine(true);
        }
    }

    /**
     * defuse($x, $y)
     * The method to call when a player wishes to try their luck at defusing a mine.
     *
     * @param $x int
     * @param $y int
     *
     * @throws \Exception
     */
    public function defuse($x, $y)
    {
        if ($this->gameOver === true) {
            return;
        }

        if ($this->boundsCheck($x, $y) === false) {
            throw new Exception("Out of bounds: " . $x . $y);
        }

        echo PHP_EOL . PHP_EOL;
        echo 'Defusing (' . $x . ', ' . $y . ')';
        echo PHP_EOL;

        try {
            $result = $this->boardMap[$x][$y]->isMine();
        } catch (Exception $e) {
            throw new Exception("Error Processing Request: " . $x . $y);
        }

        if ($result === true) {
            $this->printAnswer();
            $this->gameOver = true;
            echo PHP_EOL . 'You hit a mine.' . "\n" . 'Game over!';
            echo PHP_EOL;
            echo 'Number of recursions to solve ' . self::$loopCount;
        } else {
            $this->boardMap[$x][$y]->setTripped(true);
            $this->testAdjacentTo($x, $y);
            $this->printMap();
        }
    }

    private function testAdjacentTo($x, $y)
    {
        self::$loopCount = self::$loopCount + 1;

        if ($this->boundsCheck($x, $y) === true) { // Are we within the bounds of the map?

            $volatility = $this->squareVolatility($x, $y); // Are we at a square next to a mine?
            $this->boardMap[$x][$y]->setVolatility($volatility);

            if ($volatility === 0) {
                $this->boardMap[$x][$y]->setTripped(true); // change this square to disarmed.

                foreach ($this->adjacents as $key => $position) {

                    if ($this->debug === true) {
                        echo PHP_EOL;
                        echo PHP_EOL;
                        echo 'The next relative position is: ' . $key . ' [' . $position['x'] . ', ' . $position['y'] . '],';
                        echo PHP_EOL;
                        echo 'which is an absolute position of: (' . ($x + $position['x']) . ', ' . ($y + $position['y']) . ')';
                    }

                    /*
                    This next chunk of code is interesting.
                    Assume that you are at square (m, n) and you know you have to move to
                    square (m+1, n+1), before the move happens and we call this function
                    recursively, lets test a few things.
                    A) Is it a valid square (in bounds of map).
                    B) Has it been tripped (have we been here).
                    */

                    if ($this->boundsCheck($x + $position['x'], $y + $position['y']) === true) { // A) is it in bounds?
                        $tripped = $this->boardMap[$x + $position['x']][$y + $position['y']]->tripped(); // B) have we been here?
                        if ($tripped === false) {
                            $this->testAdjacentTo(($x + $position['x']), ($y + $position['y']));
                        }
                    }
                }

            } elseif ($volatility > 0) {
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
    private function squareVolatility($x, $y)
    {
        $volatility = 0;
        foreach ($this->adjacents as $key => $position) {
            $newX = $x - $position['x'];
            $newY = $y - $position['y'];
            if ($this->boundsCheck($newX, $newY) === true) {
                $isMine = $this->boardMap[$newX][$newY]->isMine();
                if ($isMine === true) {
                    $volatility++;
                }
            }
        }

        return $volatility;
    }

    public function printMap()
    {
        echo self::MAP_CORNER;
        for ($y = 0; $y < $this->width(); $y++) {
            echo self::MAP_EDGE_TOPS;
        }
        echo self::MAP_CORNER;

        echo PHP_EOL;

        for ($x = 0; $x < $this->height(); $x++) {
            echo self::MAP_EDGE_SIDE;
            for ($y = 0; $y < $this->width(); $y++) {
                $volatility = $this->squareVolatility($x, $y);
                $this->boardMap[$x][$y]->setVolatility($volatility);
                if ($this->gameOver === true) {
                    echo $this->boardMap[$x][$y]->printSquare(true);
                } else {
                    echo $this->boardMap[$x][$y]->printSquare(false);
                }
            }
            echo self::MAP_EDGE_SIDE;
            echo PHP_EOL;
        }

        echo self::MAP_CORNER;
        for ($y = 0; $y < $this->width(); $y++) {
            echo self::MAP_EDGE_TOPS;
        }
        echo self::MAP_CORNER;
    }

    private function printAnswer()
    {
        $this->gameOver = true;
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
    private function boundsCheck($x, $y)
    {
        if ($x >= 0 && $x < $this->height()) {
            if ($y >= 0 && $y < $this->width()) {
                return true;
            }
        }

        return false;
    }

}
