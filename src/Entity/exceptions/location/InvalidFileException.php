<?php
declare(strict_types = 1);

namespace App\Entity\exceptions\location;

use App\Entity\grid\Location;

class InvalidFileException extends InvalidLocationException {
    public function __toString(): string {
        return "File should be between {${Location::MIN_FILE}} and {${Location::MAX_FILE}}";
    }
}