<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\exceptions\move\IdenticalMoveException;
use App\exceptions\move\MoveToOccupiedByAllySquareException;
use Doctrine\ORM\Mapping as ORM;
use App\exceptions\move\InvalidPieceMoveException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class King extends Piece {

    const ID = Queen::ID + 1;
    const NAME = "K";

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @return bool
     * @throws InvalidPieceMoveException
     * @throws IdenticalMoveException
     * @throws MoveToOccupiedByAllySquareException
     */
    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);
        $this->checkOneSquareMove($targetLocation);

        return true;
    }

    /**
     * @param Location $targetLocation
     * @throws InvalidPieceMoveException
     */
    private function checkOneSquareMove(Location $targetLocation) {
        $startFile = $this->getLocation()->getChessFile();
        $startRank = $this->getLocation()->getChessRank();
        $targetFile = $targetLocation->getChessFile();
        $targetRank = $targetLocation->getChessRank();
        $fileDiff = $targetFile - $startFile;
        $rankDiff = $targetRank - $startRank;

        if (abs($fileDiff) + abs($rankDiff) != 1) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }
    }
}