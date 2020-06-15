<?php
declare(strict_types = 1);

namespace App\exceptions\move;

use App\Entity\grid\Location;

/**
 * Class NotAPieceException
 * @package App\exceptions\move
 */
class NotAPieceException extends InvalidMoveException {
    /**
     * @var Location
     */
    private Location $location;

    /**
     * NotAPieceException constructor.
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
        return "No piece at $this->location";
    }
}