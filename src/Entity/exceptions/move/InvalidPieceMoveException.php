<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\move;

use App\Entity\grid\Location;
use App\Entity\piece\Piece;

class InvalidPieceMoveException extends InvalidMoveException {
    private Piece $piece;
    private Location $targetLocation;

    /**
     * InvalidPieceMoveException constructor.
     * @param Piece $piece
     * @param Location $targetLocation
     */
    public function __construct(Piece $piece, Location $targetLocation) {
        $this->piece = $piece;
        $this->targetLocation = $targetLocation;
        parent::__construct();
    }

    public function __toString(): string {
        return "$this->targetLocation is unreachable for piece $this->piece at {$this->piece->getLocation()}";
    }
}