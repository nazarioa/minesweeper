<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../vendor/autoload.php';


class SpaceTest extends TestCase
{
    public function testIsMine()
    {
        $space = new Minesweeper\Space();
        $space->setMine(true);
        $this->assertEquals($space->isMine(), true);
    }
}


// $stack = [];
// $this->assertEquals(0, count($stack));
//
// array_push($stack, 'foo');
// $this->assertEquals('foo', $stack[count($stack)-1]);
// $this->assertEquals(1, count($stack));
//
// $this->assertEquals('foo', array_pop($stack));
// $this->assertEquals(0, count($stack));
