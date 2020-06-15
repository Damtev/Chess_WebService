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
class Knight extends Piece {

    const ID = Pawn::ID + 1;
    const NAME = "H";

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

        $startFile = $this->getLocation()->getChessFile();
        $startRank = $this->getLocation()->getChessRank();
        $targetFile = $targetLocation->getChessFile();
        $targetRank = $targetLocation->getChessRank();
        $fileDiff = $targetFile - $startFile;
        $rankDiff = $targetRank - $startRank;

        $this->checkLMove($targetLocation, $fileDiff, $rankDiff);

        return true;
    }

    /**
     * @param Location $targetLocation
     * @param int $fileDiff
     * @param int $rankDiff
     * @throws InvalidPieceMoveException
     */
    private function checkLMove(Location $targetLocation, int $fileDiff, int $rankDiff) {
        $long = abs($rankDiff) == 2 && abs($fileDiff) == 1;
        $short = abs($rankDiff) == 1 && abs($fileDiff) == 2;
        if (!($long || $short)) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }
    }
}