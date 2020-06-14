<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\exceptions\move\IdenticalMoveException;
use App\Entity\exceptions\move\InvalidMoveException;
use App\Entity\exceptions\move\MoveToCheckException;
use App\Entity\exceptions\move\MoveToOccupiedByAllySquareException;
use App\Entity\grid\Grid;
use App\Entity\grid\Location;
use App\Entity\player\Player;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr")
 * @ORM\DiscriminatorMap({"square" = "Square", "bishop" = "Bishop", "king" = "King", "knight" = "Knight", "pawn" = "Pawn", "queen" = "Queen", "rook" = "Rook"})
 */
abstract class Piece {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\grid\Grid", inversedBy="squares")
     */
    private Grid $grid;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\grid\Location", cascade={"persist"})
     */
    private Location $location;

    /**
     * @ORM\Column(type="string")
     */
    private string $locationToString;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\player\Player", inversedBy="pieces", cascade={"persist"})
     */
    private Player $player;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $moved = false;

    /**
     * Piece constructor.
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return Grid
     */
    public function getGrid(): Grid {
        return $this->grid;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid): void {
        $this->grid = $grid;
    }

    /**
     * @return string
     */
    public function getLocationToString(): string {
        return $this->locationToString;
    }

    /**
     * @param string $locationToString
     */
    public function setLocationToString(string $locationToString): void {
        $this->locationToString = $locationToString;
    }

    /**
     * @return Location
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location): void {
        $this->location = $location;
        $this->setLocationToString($location->__toString());
    }

    /**
     * @return Player
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player): void {
        $this->player = $player;
    }

    /**
     * @return bool
     */
    public function isMoved(): bool {
        return $this->moved;
    }

    /**
     * @param bool $moved
     */
    public function setMoved(bool $moved): void {
        $this->moved = $moved;
    }

    /**
     * @return int
     */
    public function id(): int {
        return static::ID;
    }

    public function __toString(): string {
        $name = static::NAME;
        return $this->toStringWithPlayer($name);
    }

    private function toStringWithPlayer(string $name) {
        if ($this->player->isWhite()) {
            return $name;
        } else {
            return strtolower($name);
        }
    }

    public function isReachableLocation(Location $targetLocation, Grid $grid): bool {
        $targetPiece = $grid[(string)$targetLocation];
        if (Piece::isEmpty($targetPiece)) {
            return true;
        }

        if ($targetPiece->getPlayer() === $this->player) {
            throw new MoveToOccupiedByAllySquareException($targetLocation);
        }

        if ($targetLocation == $this->location) {
            throw new IdenticalMoveException($targetLocation);
        }

        return true;
    }

    public function move(Location $targetLocation, Grid $grid, Player $opponent, int $transformTo) {
        $this->isReachableLocation($targetLocation, $grid);

        $this->tryMove($targetLocation, $grid, $opponent, $transformTo);
    }

    private function tryMove(Location $targetLocation, Grid $grid, Player $opponent, int $transformTo) {
        $oldLocation = $this->location;
        $targetPiece = $grid[(string)$targetLocation];
        $wasMoved = $this->isMoved();

        $white = $this->player->isWhite() ? $this->player : $opponent;
        $black = $this->player->isBlack() ? $this->player : $opponent;
        $grid->setSquare((string)$oldLocation, Square::getInstance($oldLocation, array(Player::WHITE => $white, Player::BLACK => $black)));

        if ($this instanceof Pawn && ($targetLocation->isFirstRank() || $targetLocation->isLastRank())) {
            $transformed = PieceFactory::makePiece($this->getPlayer(), $transformTo);
//            $grid[(string)$targetLocation] = $transformed;
            $grid->setSquare((string) $targetLocation, $transformed);

            $transformed->setMoved(true);
            $transformed->setLocation($targetLocation);
        } else {
//            $grid[(string)$targetLocation] = $this;
            $grid->setSquare((string) $targetLocation, $this);

            $this->setLocation($targetLocation);
            $this->setMoved(true);
        }
        if (!Piece::isEmpty($targetPiece)) {
            $opponent->deletePiece($targetPiece);
        }

        if ($this->isCheck($grid, $opponent)) {
            $this->undoMove($oldLocation, $targetLocation, $targetPiece, $wasMoved, $grid, $opponent);

            throw new MoveToCheckException($targetLocation);
        }
    }

    public function undoMove(Location $oldLocation, Location $targetLocation, Piece $targetPiece, bool $wasMoved, Grid $grid, Player $opponent) {
//        $grid[(string)$oldLocation] = $this;
        $grid->setSquare((string) $oldLocation, $this);


//        $grid[(string)$targetLocation] = $targetPiece;
        $grid->setSquare((string) $targetLocation, $targetPiece);


        $this->setLocation($oldLocation);
        $this->setMoved($wasMoved);
        if (!Piece::isEmpty($targetPiece)) {
            $targetPiece->setLocation($targetLocation);
            $opponent->addPiece($targetPiece);
        }


    }

    private function isCheck(Grid $grid, Player $opponent): bool {
        foreach ($opponent->getPieces() as $piece) {
            try {
                if ($piece->isReachableLocation($this->player->getKingLocation(), $grid)) {
                    return true;
                }
            } catch (InvalidMoveException $ignored) {
                // ignore
            }
        }

        return false;
    }

    public static function isEmpty(Piece $piece): bool {
        return $piece instanceof Square;
    }
}