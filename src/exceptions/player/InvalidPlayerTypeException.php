<?php
declare(strict_types = 1);

namespace App\exceptions\player;

use App\Entity\player\Player;

/**
 * Class InvalidPlayerTypeException
 * @package App\exceptions\player
 */
class InvalidPlayerTypeException extends InvalidPlayerException {
    /**
     * @return string
     */
    public function __toString(): string {
        return "Player should be {${Player::PLAYER_TYPES[Player::WHITE]}} or {${Player::PLAYER_TYPES[Player::BLACK]}}";
    }
}