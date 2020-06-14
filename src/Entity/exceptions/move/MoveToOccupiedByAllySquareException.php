<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\move;

use App\Entity\grid\Location;

class MoveToOccupiedByAllySquareException extends InvalidMoveException {
    private Location $location;

    /**
     * MoveToOccupiedByAllySquareException constructor.
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->location = $location;
        parent::__construct();
    }

    public function __toString() {
        return "$this->location is occupied by an allied piece";
    }
}