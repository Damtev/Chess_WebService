<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\exceptions\location\InvalidFileException;
use App\exceptions\location\InvalidRankException;
use App\exceptions\move\IdenticalMoveException;
use App\exceptions\move\InvalidPieceMoveException;
use App\exceptions\move\MoveThroughOccupiedSquareException;
use App\exceptions\move\MoveToOccupiedByAllySquareException;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class Rook extends LongMovablePiece {

    const ID = Bishop::ID + 1;
    const NAME = "R";

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @return bool
     * @throws InvalidFileException
     * @throws InvalidRankException
     * @throws IdenticalMoveException
     * @throws InvalidPieceMoveException
     * @throws MoveThroughOccupiedSquareException
     * @throws MoveToOccupiedByAllySquareException
     */
    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);
        $this->checkStraightLine($targetLocation, $grid);

        return true;
    }
}