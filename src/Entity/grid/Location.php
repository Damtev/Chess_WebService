<?php
declare(strict_types = 1);

namespace App\Entity\grid;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\exceptions\location\InvalidFileException;
use App\Entity\exceptions\location\InvalidRankException;

/**
 * @ORM\Entity
 * @ORM\Table(name="locations")
 */
class Location {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $chessFile;
    /**
     * @ORM\Column(type="integer")
     */
    private int $chessRank;

    public const MIN_RANK = 1;
    public const MAX_RANK = 8;
    public const MIN_FILE = 'a';
    public const MAX_FILE = 'h';



    /**
     * Location constructor.
     * @param string $file
     * @param int $rank
     */
    private function __construct(string $file, int $rank) {
        $this->chessFile = ord($file) - ord(Location::MIN_FILE);
        $this->chessRank = $rank;
    }

    public static function getInstance(string $file, int $rank): Location {
        $file = strtolower($file);
        if ($file < self::MIN_FILE || $file > self::MAX_FILE) {
            throw new InvalidFileException();
        }

        if ($rank < self::MIN_RANK || $rank > self::MAX_RANK) {
            throw new InvalidRankException();
        }

        return new Location($file, $rank);
    }

    public static function getInstanceFromString(string $location): Location {
        return self::getInstance($location[0], (int) $location[1]);
    }

    public static function getInstanceFromInt(int $file, int $rank): Location {
        return self::getInstance(chr(ord("a") + $file), $rank);
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getChessFile(): int {
        return $this->chessFile;
    }

    /**
     * @return int
     */
    public function getChessRank(): int {
        return $this->chessRank;
    }
    
    public function isFirstRank(): bool {
        return $this->chessRank == 1;
    }

    public function isLastRank(): bool {
        return $this->chessRank == 8;
    }

    public function fileToString(): string {
        return chr(ord("a") + $this->chessFile);
    }

    public static function fileToInt(string $file): int {
        return ord($file) - ord(Location::MIN_FILE);
    }

    public function __toString(): string {
        $fileToString = $this->fileToString();
        return "$fileToString$this->chessRank";
    }

    /**
     * @param int $chessFile
     */
    public function setChessFile(int $chessFile): void {
        $this->chessFile = $chessFile;
    }

    /**
     * @param int $chessRank
     */
    public function setChessRank(int $chessRank): void {
        $this->chessRank = $chessRank;
    }
}