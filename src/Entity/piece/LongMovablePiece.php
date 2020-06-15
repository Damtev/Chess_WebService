<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\exceptions\location\InvalidFileException;
use App\exceptions\location\InvalidRankException;
use App\exceptions\move\InvalidPieceMoveException;
use App\exceptions\move\MoveThroughOccupiedSquareException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * Class LongMovablePiece
 * @package App\Entity\piece
 */
class LongMovablePiece extends Piece {
    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @throws InvalidPieceMoveException
     * @throws MoveThroughOccupiedSquareException
     * @throws InvalidFileException
     * @throws InvalidRankException
     */
    protected function checkDiagonalLine(Location $targetLocation, Grid $grid): void {
        $startFile = $this->getLocation()->getChessFile();
        $startRank = $this->getLocation()->getChessRank();
        $targetFile = $targetLocation->getChessFile();
        $targetRank = $targetLocation->getChessRank();

        $fileDiff = abs($this->getLocation()->getChessFile() - $targetLocation->getChessFile());
        $rankDiff = abs($this->getLocation()->getChessRank() - $targetLocation->getChessRank());
        if ($fileDiff != $rankDiff) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }

        $this->checkContinuousMove($grid, $startFile, $startRank, $targetFile, $targetRank);
    }

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @throws InvalidFileException
     * @throws InvalidPieceMoveException
     * @throws InvalidRankException
     * @throws MoveThroughOccupiedSquareException
     */
    protected function checkStraightLine(Location $targetLocation, Grid $grid): void {
        $startFile = $this->getLocation()->getChessFile();
        $startRank = $this->getLocation()->getChessRank();
        $targetFile = $targetLocation->getChessFile();
        $targetRank = $targetLocation->getChessRank();
        $fileDiff = $targetFile - $startFile;
        $rankDiff = $targetRank - $startRank;

        $vertical = $fileDiff == 0;
        $horizontal = $rankDiff == 0;
        if (!($vertical || $horizontal)) {
            throw new InvalidPieceMoveException($this, $targetLocation);
        }

        $this->checkContinuousMove($grid, $startFile, $startRank, $targetFile, $targetRank);
    }

    /**
     * @param Grid $grid
     * @param int $startFile
     * @param int $startRank
     * @param int $targetFile
     * @param int $targetRank
     * @throws MoveThroughOccupiedSquareException
     * @throws InvalidFileException
     * @throws InvalidRankException
     */
    protected function checkContinuousMove(Grid $grid, int $startFile, int $startRank, int $targetFile, int $targetRank) {
        $fileDiff = $targetFile - $startFile;
        $rankDiff = $targetRank - $startRank;

        $fileChange = $fileDiff > 0 ? 1 : ($fileDiff == 0 ? 0 : -1);
        $rankChange = $rankDiff > 0 ? 1 : ($rankDiff == 0 ? 0 : -1);
        // check empty squares
        for ($file = $startFile + $fileChange, $rank = $startRank + $rankChange;
             $file != $targetFile, $rank != $targetRank;
             $file += $fileChange, $rank += $rankChange) {
            $curLocation = Location::getInstanceFromInt($file, $rank);
            if (!Piece::isEmpty($grid[(string)$curLocation])) {
                throw new MoveThroughOccupiedSquareException($curLocation);
            }
        }
    }
}