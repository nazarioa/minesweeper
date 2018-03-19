<?php

namespace Minesweeper;

use Exception;

class Space {
  const MINE = '*';
  const SAFE = ' ';
  const HIDDEN = '•';

  private $tripped = FALSE;
  private $volatility = 0;
  private $type = self::SAFE;
  private $debug = FALSE;

  public function __construct( array $options = array() ) {
    $this->type       = self::SAFE;
    $this->tripped    = FALSE;
    $this->volatility = 0;
    $this->debug      = FALSE;

    if ( isset( $options['debug'] ) && $options['debug'] === TRUE ) {
      $this->debug = TRUE;
    }
  }

  public function setTripped( $tripped = FALSE ) {
    $this->tripped = $tripped;
  }

  public function tripped() {
    return $this->tripped;
  }

  /**
   * @param $volatility
   *
   * @throws \Exception
   */
  public function setVolatility( $volatility ) {
    if ( $this->debug === TRUE ) {
      echo $volatility;
    }

    if ( ! is_numeric( $volatility ) ) {
      throw new Exception( 'volatility must be a number' );
    }

    $this->volatility = $volatility;
  }

  public function volatility() {
    if ( $this->debug === TRUE ) {
      echo $this->volatility;
    }

    return $this->volatility;
  }

  public function setMine( $is_mine = FALSE ) {
    if ( $is_mine === TRUE ) {
      $this->type = self::MINE;
    } else {
      $this->type = self::SAFE;
    }
  }

  public function mine() {
    return $this->type;
  }

  public function isMine() {
    return $this->type === self::MINE;
  }

  public function printSquare( $revealAnswer = FALSE ) {
    if ( $revealAnswer === TRUE ) {
      if ( $this->isMine() === TRUE ) {
        echo $this->type;
      } else {
        echo $this->volatility === 0 ? Space::SAFE : $this->volatility;
      }
    } else {
      if ( $this->tripped === FALSE ) {
        echo self::HIDDEN;
      } else {
        echo $this->volatility === 0 ? Space::SAFE : $this->volatility;
      }
    }
  }
}
