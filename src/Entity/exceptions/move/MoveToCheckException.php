<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\move;

use App\Entity\grid\Location;

class MoveToCheckException extends InvalidMoveException {
    private Location $targetLocation;

    /**
     * MoveToCheckException constructor.
     * @param Location $targetLocation
     */
    public function __construct(Location $targetLocation) {
        $this->targetLocation = $targetLocation;
        parent::__construct();
    }

    public function __toString(): string {
        return "Move to $this->targetLocation leads to check for your king, it is not allowed";
    }
}