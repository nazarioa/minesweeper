<?php
class Space {
  const MINE = '*';
  const SAFE = ' ';
  const HIDDEN = '•';

  private $tripped = false;
  private $volatility = 0;
  private $type = self::SAFE;
  private $adjacent = array();

  public function __construct () {
    $this->type = self::SAFE;
    $this->tripped = false;
    $this->volatility = 0;
  }

  public function setTripped ($tripped = false) {
    $this->tripped = $tripped;
  }

  public function setVolatility ($volatility) {
    $this->volatility = $volatility;
  }

  public function volatility () {
    return $this->volatility;
  }

  public function setMine ($is_mine = false) {
    if ( $is_mine == true) {
      $this->type = self::MINE;
    } else {
      $this->type = self::SAFE;
    }
  }

  public function mine (){
    return $this->type;
  }

  public function isMine () {
    if( $this->type == self::MINE ) {
      return true;
    } else {
      return false;
    }
  }

  public function printSquare ($giveAnswer = false) {
    if($giveAnswer == true) {
      if ($this->isMine() == true) {
        echo $this->type;
      } else {
        echo $this->volatility;
      }
    } else {
      if ($this->tripped == false) {
        echo self::HIDDEN;
      } else {
        if ($this->volatility == 0){
          echo self::SAFE;
        } else {
          echo $this->volatility;
        }
      }
    }
  }
}
