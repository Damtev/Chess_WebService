<?php
declare(strict_types = 1);

namespace App\Entity\player;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\exceptions\player\InvalidPlayerTypeException;
use App\Entity\game\GameState;
use App\Entity\grid\Location;
use App\Entity\piece\Piece;

/**
 * @ORM\Entity
 */
class Player {

    public const WHITE = 0;
    public const BLACK = 1;
    public const PLAYER_TYPES = array(self::WHITE => "WHITE", self::BLACK => "BLACK");

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\game\GameState", inversedBy="players")
     */
    private GameState $gameState;

    /**
     * @ORM\Column(type="integer")
     */
    private int $playerType;

    /**
     * @var Piece[] $pieces
     * @ORM\OneToMany(targetEntity="App\Entity\piece\Piece", mappedBy="player")
     */
    private $pieces = array();

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isInCheck = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\grid\Location")
     */
    private ?Location $kingLocation = null;

    /**
     * Player constructor.
     * @param int $playerType
     */
    private function __construct(int $playerType) {
        $this->playerType = $playerType;
    }

    public static function getInstance(int $playerType) {
        if (key_exists($playerType, self::PLAYER_TYPES)) {
            return new Player($playerType);
        }

        throw new InvalidPlayerTypeException();
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return GameState
     */
    public function getGameState(): GameState {
        return $this->gameState;
    }

    /**
     * @param GameState $gameState
     */
    public function setGameState(GameState $gameState): void {
        $this->gameState = $gameState;
    }

    /**
     * @param int $playerType
     */
    public function setPlayerType(int $playerType): void {
        $this->playerType = $playerType;
    }

    /**
     * @return Piece[]
     */
    public function getPieces() {
        return $this->pieces;
    }

    /**
     * @param Piece[] $pieces
     */
    public function setPieces($pieces): void {
        $this->pieces = $pieces;
    }

    /**
     * @return string
     */
    public function getPlayerType(): string {
        return self::PLAYER_TYPES[$this->playerType];
    }

    /**
     * @return Location|null
     */
    public function getKingLocation(): ?Location {
        return $this->kingLocation;
    }

    /**
     * @param Location $kingLocation
     */
    public function setKingLocation(Location $kingLocation): void {
        $this->kingLocation = $kingLocation;
    }

    /**
     * @return bool
     */
    public function isInCheck(): bool {
        return $this->isInCheck;
    }

    /**
     * @param bool $isInCheck
     */
    public function setIsInCheck(bool $isInCheck): void {
        $this->isInCheck = $isInCheck;
    }

    public function addPiece(Piece $piece): bool {
        $pieceLocation = $piece->getLocation();
        foreach ($this->pieces as $existingPiece) {
            if ($existingPiece->getLocation() == $pieceLocation) {
                return false;
            }
        }

        return true;
    }

    public function deletePiece(Piece $piece) {
        $key = array_search($piece, $this->getPieces());
        unset($this->pieces[$key]);
        $this->pieces = array_values($this->pieces);
    }

    public function __toString(): string {
        return $this->getPlayerType();
    }

    public function isWhite(): bool {
        return $this->getPlayerType() == self::PLAYER_TYPES[self::WHITE];
    }

    public function isBlack(): bool {
        return !$this->isWhite();
    }
}