<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\exceptions\move\InvalidMoveException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class Queen extends LongMovablePiece {

    const ID = Rook::ID + 1;
    const NAME = "Q";

    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);
        try {
            $this->checkStraightLine($targetLocation, $grid);

            return true;
        } catch (InvalidMoveException $exception) {
            $this->checkDiagonalLine($targetLocation, $grid);
        }

        return true;
    }
}