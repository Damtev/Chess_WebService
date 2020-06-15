<?php
declare(strict_types = 1);

namespace App\exceptions\move;

use App\Entity\grid\Location;

/**
 * Class IdenticalMoveException
 * @package App\exceptions\move
 */
class IdenticalMoveException extends InvalidMoveException {

    /**
     * @var Location
     */
    private Location $location;

    /**
     * IdenticalMoveException constructor.
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
        return "Cannot move to current location $this->location";
    }
}