<?php
declare(strict_types = 1);

namespace App\exceptions\move;

use App\Entity\grid\Location;

/**
 * Class MoveToCheckException
 * @package App\exceptions\move
 */
class MoveToCheckException extends InvalidMoveException {
    /**
     * @var Location
     */
    private Location $targetLocation;

    /**
     * MoveToCheckException constructor.
     * @param Location $targetLocation
     */
    public function __construct(Location $targetLocation) {
        $this->targetLocation = $targetLocation;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return "Move to $this->targetLocation leads to check for your king, it is not allowed";
    }
}