<?php
/**
 * Created by PhpStorm.
 * User: nazario
 * Date: 3/15/18
 * Time: 5:09 PM
 */

use PHPUnit\Framework\TestCase;
use Minesweeper\Board;

class BoardTest extends TestCase
{

    /**
     * @test
     */
    public function game_init()
    {
        $board = Minesweeper\Board::game_init(3, 3, [['x' => 0, 'y' => 0]], ['debug' => false]);
        $this->expectOutputString('+---+
|•••|
|•••|
|•••|
+---+');
    }

    /**
     * @test
     */
    public function defuseAndHitABomb()
    {
        $board = Minesweeper\Board::game_init(3, 3, [['x' => 0, 'y' => 0]], ['debug' => false]);

        $this->expectOutputString('+---+
|•••|
|•••|
|•••|
+---+

Defusing (0, 0)
+---+
|*1 |
|11 |
|   |
+---+
You hit a mine.
Game over!
Number of recursions to solve 0');
        echo $board->defuse(0, 0);
    }

    /**
     * @test
     */
    public function defuseAndNotHitABomb()
    {
        $board = Minesweeper\Board::game_init(3, 3, [['x' => 0, 'y' => 0]], ['debug' => false]);

        $this->expectOutputString('+---+
|•••|
|•••|
|•••|
+---+

Defusing (1, 1)
+---+
|•••|
|•1•|
|•••|
+---+');
        echo $board->defuse(1, 1);
    }

    /**
     * @test
     */
    public function tryingToDefuseOutOfBound()
    {
        $board = Minesweeper\Board::game_init(3, 3, [['x' => 0, 'y' => 0]], ['debug' => false]);
        $this->expectOutputString('+---+
|•x•|
|•••|
|•••|
+---+Out of bounds.');
        echo $board->defuse(3, 3);
    }
}
