<?php
declare(strict_types = 1);

namespace App\exceptions\move;

use App\Entity\grid\Location;

/**
 * Class MoveToOccupiedByAllySquareException
 * @package App\exceptions\move
 */
class MoveToOccupiedByAllySquareException extends InvalidMoveException {
    /**
     * @var Location
     */
    private Location $location;

    /**
     * MoveToOccupiedByAllySquareException constructor.
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->location = $location;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return "$this->location is occupied by an allied piece";
    }
}