<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\move;

use App\Entity\grid\Location;

class NotAPieceException extends InvalidMoveException {
    private Location $location;

    /**
     * NotAPieceException constructor.
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->location = $location;
        parent::__construct();
    }

    public function __toString(): string {
        return "No piece at $this->location";
    }
}