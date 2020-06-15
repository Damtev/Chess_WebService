<?php
declare(strict_types = 1);

namespace App\exceptions\location;

use App\Entity\grid\Location;

/**
 * Class InvalidRankException
 * @package App\exceptions\location
 */
class InvalidRankException extends InvalidLocationException {
    /**
     * @return string
     */
    public function __toString(): string {
        return "Rank should be between {${Location::MIN_RANK}} and {${Location::MAX_RANK}}";
    }
}