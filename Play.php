<?php
require_once ('Space.php');
require_once ('Minesweeper.php');


$mines = [
  ['x' => 0, 'y' => 0],
  ['x' => 1, 'y' => 0],
  ['x' => 2, 'y' => 0],

  ['x' => 0, 'y' => 1],
  ['x' => 2, 'y' => 1],

  ['x' => 0, 'y' => 2],
  ['x' => 1, 'y' => 2],
  ['x' => 2, 'y' => 2],

  ['x' => 3, 'y' =>3],

  ['x' => 4, 'y' =>4],
];


$minesweeper = new Minesweeper (5, 7, $mines, array('debug' => false));
$minesweeper->defuse(1,1); // no bomb
$minesweeper->defuse(4,2); // no bomb
$minesweeper->defuse(6,0); // no bomb
$minesweeper->defuse(0,4); // no bomb
$minesweeper->defuse(2,1); // bomb
$minesweeper->defuse(2,3); // no bomb
$minesweeper->defuse(1,1); // no bomb
