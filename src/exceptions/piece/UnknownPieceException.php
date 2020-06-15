<?php
declare(strict_types = 1);

namespace App\exceptions\piece;

use Exception;

/**
 * Class UnknownPieceException
 * @package App\exceptions\piece
 */
class UnknownPieceException extends Exception {
    /**
     * @var int
     */
    private int $id;
    /**
     * UnknownPieceException constructor.
     * @param int $id
     */
    public function __construct(int $id) {
        $this->id = $id;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return "No piece with such $this->id";
    }
}