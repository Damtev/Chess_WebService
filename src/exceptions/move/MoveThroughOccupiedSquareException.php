<?php
declare(strict_types = 1);

namespace App\exceptions\move;

use App\Entity\grid\Location;

/**
 * Class MoveThroughOccupiedSquareException
 * @package App\exceptions\move
 */
class MoveThroughOccupiedSquareException extends InvalidMoveException {
    /**
     * @var Location
     */
    private Location $location;

    /**
     * MoveThroughOccupiedSquareException constructor.
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
        return "Cannot move through occupied square $this->location";
    }
}