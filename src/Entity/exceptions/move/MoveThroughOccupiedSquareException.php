<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\move;

use App\Entity\grid\Location;

class MoveThroughOccupiedSquareException extends InvalidMoveException {
    private Location $location;

    /**
     * MoveThroughOccupiedSquareException constructor.
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->location = $location;
        parent::__construct();
    }

    public function __toString(): string {
        return "Cannot move through occupied square $this->location";
    }
}