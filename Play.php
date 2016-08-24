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
];


$minesweeper = new Minesweeper (5, 5, $mines, array('debug' => true));
// $minesweeper->defuse(1,1); // no bomb
$minesweeper->defuse(4,4); // no bomb
// $Minesweeper->printMap();
// $minesweeper->defuse(1,0); // no bomb
// $minesweeper->defuse(3,3); // no bomb
// $minesweeper->defuse(2,3); // no bomb
// $minesweeper->defuse(2,1); // no bomb
// $minesweeper->defuse(2,3); // no bomb
// $minesweeper->defuse(1,1); // bomb
