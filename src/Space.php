<?php
namespace Minesweeper;

class Space {
  const MINE = '*';
  const SAFE = ' ';
  const HIDDEN = '•';

  private $tripped = false;
  private $volatility = 0;
  private $type = self::SAFE;
  private $adjacent = array();
  private $debug = false;

  public function __construct (array $options = array()) {
    $this->type = self::SAFE;
    $this->tripped = false;
    $this->volatility = 0;
    $this->debug = false;

    if ( isset($options['debug']) &&  $options['debug'] === true) {
      $this->debug = true;
    }
  }

  public function setTripped ($tripped = false) {
    $this->tripped = $tripped;
  }

  public function tripped() {
    return $this->tripped;
  }

  public function setVolatility ($volatility) {
    if($this->debug === true) {
      echo $volatility;
    }

    if(!is_numeric($volatility)) {
      throw Exception('volatility must be a number');
    }

    $this->volatility = $volatility;
  }

  public function volatility () {
    if($this->debug === true) {
      echo  $this->volatility;
    }

    return $this->volatility;
  }

  public function setMine ($is_mine = false) {
    if ( $is_mine === true) {
      $this->type = self::MINE;
    } else {
      $this->type = self::SAFE;
    }
  }

  public function mine (){
    return $this->type;
  }

  public function isMine () {
    if( $this->type === self::MINE ) {
      return true;
    } else {
      return false;
    }
  }

  public function printSquare ($giveAnswer = false) {
    if($giveAnswer === true) {
      if ($this->isMine() === true) {
        echo $this->type;
      } else {
        echo $this->volatility;
      }
    } else {
      if ($this->tripped === false) {
        echo self::HIDDEN;
      } else {
        if ($this->volatility === 0){
          echo self::SAFE;
        } else {
          echo $this->volatility;
        }
      }
    }
  }
}
