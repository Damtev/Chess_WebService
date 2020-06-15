<?php
declare(strict_types = 1);

namespace App\exceptions\location;

use App\Entity\grid\Location;

/**
 * Class InvalidFileException
 * @package App\exceptions\location
 */
class InvalidFileException extends InvalidLocationException {
    /**
     * @return string
     */
    public function __toString(): string {
        return "File should be between {${Location::MIN_FILE}} and {${Location::MAX_FILE}}";
    }
}