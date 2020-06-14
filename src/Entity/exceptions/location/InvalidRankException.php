<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\location;

use App\Entity\grid\Location;

class InvalidRankException extends InvalidLocationException {
    public function __toString(): string {
        return "Rank should be between {${Location::MIN_RANK}} and {${Location::MAX_RANK}}";
    }
}