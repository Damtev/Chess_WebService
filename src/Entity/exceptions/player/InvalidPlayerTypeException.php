<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\player;

use App\Entity\player\Player;

class InvalidPlayerTypeException extends InvalidPlayerException {
    public function __toString(): string {
        return "Player should be {${Player::PLAYER_TYPES[Player::WHITE]}} or {${Player::PLAYER_TYPES[Player::BLACK]}}";
    }
}