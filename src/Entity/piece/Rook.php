<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;

/**
 * @ORM\Entity
 */
class Rook extends LongMovablePiece {

    const ID = Bishop::ID + 1;
    const NAME = "R";

    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        parent::isReachableLocation($targetLocation, $grid);
        $this->checkStraightLine($targetLocation, $grid);

        return true;
    }
}