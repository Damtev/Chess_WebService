<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\exceptions\move\IdenticalMoveException;
use App\exceptions\move\InvalidPieceMoveException;
use App\exceptions\move\MoveToOccupiedByAllySquareException;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class Bishop extends LongMovablePiece {

    const ID = Knight::ID + 1;
    const NAME = "B";

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @return bool
     * @throws IdenticalMoveException
     * @throws InvalidPieceMoveException
     * @throws MoveToOccupiedByAllySquareException
     */
    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);
        $this->checkDiagonalLine($targetLocation, $grid);

        return true;
    }
}