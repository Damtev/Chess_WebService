<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\Entity\exceptions\move\InvalidPieceMoveException;
use App\Entity\exceptions\move\MoveThroughOccupiedSquareException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

class LongMovablePiece extends Piece {
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

        $this->checkContinuousMove($targetLocation, $grid, $startFile, $startRank, $targetFile, $targetRank);
    }

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

        $this->checkContinuousMove($targetLocation, $grid, $startFile, $startRank, $targetFile, $targetRank);
    }

    protected function checkContinuousMove(Location $targetLocation, Grid $grid, int $startFile, int $startRank, int $targetFile, int $targetRank) {
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