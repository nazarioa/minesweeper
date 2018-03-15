<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../vendor/autoload.php';


class SpaceTest extends TestCase
{
    public function testSetMineAndIsMine()
    {
        $space = new Minesweeper\Space();
        $space->setMine(true);
        $this->assertEquals($space->isMine(), true);
    }
}
