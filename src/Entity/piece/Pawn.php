<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\exceptions\location\InvalidFileException;
use App\exceptions\location\InvalidRankException;
use App\exceptions\move\IdenticalMoveException;
use App\exceptions\move\MoveToOccupiedByAllySquareException;
use Doctrine\ORM\Mapping as ORM;
use App\exceptions\move\InvalidPieceMoveException;
use App\exceptions\move\MoveThroughOccupiedSquareException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class Pawn extends Piece {

    const ID = Square::ID + 1;
    const NAME = "P";

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @return bool
     * @throws IdenticalMoveException
     * @throws InvalidPieceMoveException
     * @throws MoveThroughOccupiedSquareException
     * @throws MoveToOccupiedByAllySquareException
     * @throws InvalidFileException
     * @throws InvalidRankException
     */
    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);

        $startFile = $this->getLocation()->getChessFile();
        $startRank = $this->getLocation()->getChessRank();
        $targetFile = $targetLocation->getChessFile();
        $targetRank = $targetLocation->getChessRank();
        $fileDiff = $targetFile - $startFile;
        $rankDiff = $targetRank - $startRank;

        $this->checkVerticalLineMove($targetLocation, $fileDiff, $rankDiff);
        $this->checkTwoSquaresMove($targetLocation, $grid, $rankDiff);
        $this->checkDiagonalMove($targetLocation, $grid, $fileDiff);

        return true;
    }

    /**
     * @param Location $targetLocation
     * @param int $fileDiff
     * @param int $rankDiff
     * @throws InvalidPieceMoveException
     */
    private function checkVerticalLineMove(Location $targetLocation, int $fileDiff, int $rankDiff) {
        if ($rankDiff == 0 ||
            abs($fileDiff) > 1 ||
            abs($rankDiff) > 2 ||
            ($this->getPlayer()->isWhite() && $rankDiff < 0) ||
            ($this->getPlayer()->isBlack() && $rankDiff > 0)) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }
    }

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @param int $rankDiff
     * @throws InvalidPieceMoveException
     * @throws MoveThroughOccupiedSquareException
     * @throws InvalidFileException
     * @throws InvalidRankException
     */
    private function checkTwoSquaresMove(Location $targetLocation, Grid $grid, int $rankDiff) {
        if ($this->isMoved() && abs($rankDiff) > 1) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }

        if (abs($rankDiff) == 2) {
            $middleLocation = Location::getInstanceFromInt($this->getLocation()->getChessFile(), $this->getLocation()->getChessRank() + ($rankDiff / 2));
            if (!Piece::isEmpty($grid[(string) $middleLocation])) {
                throw new MoveThroughOccupiedSquareException($middleLocation);
            }
        }
    }

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @param int $fileDiff
     * @throws InvalidPieceMoveException
     */
    private function checkDiagonalMove(Location $targetLocation, Grid $grid, int $fileDiff) {
        $targetPiece = $grid[(string) $targetLocation];
        if (abs($fileDiff) == 1 && Piece::isEmpty($targetPiece)) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }
    }
}