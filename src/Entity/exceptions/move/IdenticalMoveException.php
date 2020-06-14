<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\move;

use App\Entity\grid\Location;

class IdenticalMoveException extends InvalidMoveException {

    private Location $location;

    /**
     * IdenticalMoveException constructor.
     * @param Location $location
     */
    public function __construct(Location $location) {
        $this->location = $location;
        parent::__construct();
    }

    public function __toString() {
        return "Cannot move to current location $this->location";
    }
}