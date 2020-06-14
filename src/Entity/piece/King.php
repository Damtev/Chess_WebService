<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\exceptions\move\InvalidPieceMoveException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class King extends Piece {

    const ID = Queen::ID + 1;
    const NAME = "K";

    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);
        $this->checkOneSquareMove($targetLocation);

        return true;
    }

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