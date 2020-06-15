<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\Entity\grid\Grid;
use App\Entity\grid\Location;
use App\Entity\player\Player;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Square extends Piece {

    const ID = 0;
    const NAME = "S";

    /**
     * @param Location $location
     * @param array $players
     * @return Square
     */
    public static function getInstance(Location $location, array $players): Square {
        $whites = $players[Player::WHITE];
        $blacks = $players[Player::BLACK];
        $player = (int) Grid::squareColorFromLocation($location) == Player::WHITE ? $whites : $blacks;
        $square = new Square($player);
        $square->setLocation($location);

        return $square;
    }

    /**
     * @param Location $targetLocation
     * @param Grid $grid
     * @return bool
     */
    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return Grid::squareColorFromLocation($this->getLocation());
    }
}