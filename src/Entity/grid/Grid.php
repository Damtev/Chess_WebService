<?php
declare(strict_types = 1);

namespace App\Entity\grid;

use App\Entity\piece\Square;
use ArrayAccess;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\piece\Bishop;
use App\Entity\piece\King;
use App\Entity\piece\Knight;
use App\Entity\piece\Pawn;
use App\Entity\piece\Piece;
use App\Entity\piece\Queen;
use App\Entity\piece\Rook;
use App\Entity\player\Player;

/**
 * @ORM\Entity
 */
class Grid implements ArrayAccess {

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @var Piece[] $squares
     * @ORM\OneToMany(targetEntity="App\Entity\piece\Piece", indexBy="locationToString", mappedBy="grid", cascade={"persist"})
     */
    private $squares;

    /**
     * Grid constructor.
     * @param $squares
     */
    private function __construct($squares) {
        $this->squares = $squares;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return Piece[]
     */
    public function getSquares() {
        return $this->squares;
    }

    /**
     * @param Player $whites
     * @param Player $blacks
     * @return Grid
     * @throws \App\exceptions\location\InvalidFileException
     * @throws \App\exceptions\location\InvalidRankException
     */
    public static function getDefaultGrid(Player $whites, Player $blacks): Grid {
        $whiteSet = Grid::getStandardSet($whites);
        $blackSet = Grid::getStandardSet($blacks);
        $whites->setPieces($whiteSet);
        $blacks->setPieces($blackSet);

        $locations = array();
        for ($rank = Location::MAX_RANK; $rank >= Location::MIN_RANK; $rank--) {
            for ($file = Location::MIN_FILE; $file <= Location::MAX_FILE; $file++) {
                $locations[] = (string) Location::getInstance($file, $rank);
            }
        }

        $pieces = $blackSet;
        for ($rank = Location::MAX_RANK - 2; $rank >= Location::MIN_RANK + 2; $rank--) {
            for ($file = Location::MIN_FILE; $file <= Location::MAX_FILE; $file++) {
                $location = Location::getInstance($file, $rank);
                $pieces[] = Square::getInstance($location, array(Player::WHITE => $whites, Player::BLACK => $blacks));
            }
        }
        $pieces = array_merge($pieces, array_reverse($whiteSet));
        $grid = new Grid(array_combine($locations, $pieces));
        foreach ($grid->squares as $location => $piece) {
            $piece->setLocation(Location::getInstanceFromString($location));
            $piece->setGrid($grid);
        }

        return $grid;
    }

    /**
     * @param Player $player
     * @return Piece[]
     */
    private static function getStandardSet(Player $player): array {
        $firstRow = array(
            new Rook($player),
            new Knight($player),
            new Bishop($player),
            new Queen($player),
            new King($player),
            new Bishop($player),
            new Knight($player),
            new Rook($player)
        );
        if ($player->isWhite()) {
            $firstRow = array_reverse($firstRow);
        }
        $secondRow = array();
        for ($i = Location::MIN_RANK; $i <= Location::MAX_RANK; $i++) {
            $secondRow[] = new Pawn($player);
        }
        return array_merge($firstRow, $secondRow);
    }

    /**
     * @param $squares
     */
    public function setSquares($squares): void {
        $this->squares = $squares;
    }

    /**
     * @param string $location
     * @param Piece $piece
     */
    public function setSquare(string $location, Piece $piece) {
        $this->squares[$location] = $piece;
    }

    /**
     * @return string
     * @throws \App\exceptions\location\InvalidFileException
     * @throws \App\exceptions\location\InvalidRankException
     */
    public function __toString(): string {
        $toString = "  ";
        for ($file = Location::MIN_FILE; $file <= Location::MAX_FILE; $file++) {
            $toString .= $file;
        }
        $toString .= "  " . PHP_EOL;
        for ($rank = Location::MAX_RANK; $rank >= Location::MIN_RANK; $rank--) {
            $toString .= $rank . "|";
            for ($file = Location::MIN_FILE; $file <= Location::MAX_FILE; $file++) {
                $location = Location::getInstance($file, $rank);
                $piece = $this[(string)$location];
                if (Piece::isEmpty($piece)) {
                    $toString .= Grid::squareColorFromLocation($location);
                } else {
                    $toString .= (string)$piece;
                }
            }
            $toString .= "|" . $rank;
            $toString .= PHP_EOL;
        }
        $toString .= "  ";
        for ($file = Location::MIN_FILE; $file <= Location::MAX_FILE; $file++) {
            $toString .= $file;
        }
        $toString .= "  ";
        return $toString;
    }

    /**
     * @param Location $location
     * @return string
     */
    public static function squareColorFromLocation(Location $location): string {
        $fileDiff = $location->getChessFile() - Location::fileToInt(Location::MIN_FILE);
        $rankDiff = $location->getChessRank();
        if (($fileDiff + $rankDiff) % 2 == 0) {
            return (string)Player::WHITE;
        } else {
            return (string)Player::BLACK;
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->squares[] = $value;
        } else {
            $this->squares[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->squares[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->squares[$offset]);
    }

    /**
     * @param mixed $offset
     * @return Piece|mixed|null
     */
    public function offsetGet($offset) {
        return isset($this->squares[$offset]) ? $this->squares[$offset] : null;
    }
}