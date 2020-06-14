<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\piece;

use Exception;

class UnknownPieceException extends Exception {
    private int $id;
    /**
     * UnknownPieceException constructor.
     * @param int $id
     */
    public function __construct(int $id) {
        $this->id = $id;
        parent::__construct();
    }

    public function __toString(): string {
        return "No piece with such $this->id";
    }
}