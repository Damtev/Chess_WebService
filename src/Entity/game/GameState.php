<?php
declare(strict_types = 1);

namespace App\Entity\game;

use App\Entity\piece\Queen;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use App\exceptions\move\AnotherPieceOwnerException;
use App\exceptions\move\NotAPieceException;
use App\Entity\grid\Location;
use App\Entity\piece\Bishop;
use App\Entity\piece\King;
use App\Entity\piece\Knight;
use App\Entity\piece\Pawn;
use App\Entity\piece\Piece;
use App\Entity\grid\Grid;
use App\Entity\player\Player;

/**
 * @ORM\Entity
 */
class GameState {

    public const CONTINUING = "Game in a progress";
    public const DRAW = "Game was finished, it is a draw";
    public const WHITES_WIN = "Game was finished, it is a whites victory";
    public const BLACKS_WIN = "Game was finished, it is a blacks victory";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected int $id;

    /**
     * @var Player[] $players
     * @ORM\OneToMany(targetEntity="App\Entity\player\Player", mappedBy="gameState", cascade={"persist"})
     */
    private $players;

    /**
     * @ORM\Column(type="integer")
     */
    private int $curPlayer;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\grid\Grid", cascade={"persist"})
     */
    private Grid $grid;

    /**
     * @ORM\Column(type="string")
     */
    private string $curGameStatus = self::CONTINUING;

    /**
     * GameState constructor.
     * @param array|Player[] $players
     * @param int $curPlayer
     * @param Grid $grid
     */
    private function __construct($players, int $curPlayer, Grid $grid) {
        $this->players = $players;
        $this->curPlayer = $curPlayer;
        $this->grid = $grid;
    }

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return Player[]|array
     */
    public function getPlayers() {
        return $this->players;
    }

    /**
     * @return int
     */
    public function getCurPlayer(): int {
        return $this->curPlayer;
    }

    /**
     * @param Player[] $players
     */
    public function setPlayers($players): void {
        $this->players = $players;
    }

    /**
     * @param int $curPlayer
     */
    public function setCurPlayer(int $curPlayer): void {
        $this->curPlayer = $curPlayer;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid): void {
        $this->grid = $grid;
    }

    /**
     * @param string $curGameStatus
     */
    public function setCurGameStatus(string $curGameStatus): void {
        $this->curGameStatus = $curGameStatus;
    }

    /**
     * @return string
     */
    public function getCurGameStatus(): string {
        return $this->curGameStatus;
    }

    /**
     * @return Grid
     */
    public function getGrid(): Grid {
        return $this->grid;
    }

    /**
     * @return GameState
     * @throws \App\exceptions\player\InvalidPlayerTypeException
     */
    public static function startGame(): GameState {
        $whites = Player::getInstance(Player::WHITE);
        $blacks = Player::getInstance(Player::BLACK);
        $grid = Grid::getDefaultGrid($whites, $blacks);
        foreach ($grid->getSquares() as $piece) {
            if (!Piece::isEmpty($piece)) {
                if ($piece instanceof King) {
                    $piece->getPlayer()->setKingLocation($piece->getLocation());
                }
            }
        }

        $players = array($whites, $blacks);

        $gameState = new GameState($players, Player::WHITE, $grid);
        $whites->setGameState($gameState);
        $blacks->setGameState($gameState);
        return $gameState;
    }

    /**
     * @param Location $startLocation
     * @param Location $targetLocation
     * @param int $transformTo
     * @return string
     */
    public function move(Location $startLocation, Location $targetLocation, int $transformTo = Queen::ID): string {
        if ($this->isEnded()) {
            return "Move is impossible, {${lcfirst($this->curGameStatus)}}";
        }
        try {
            $this->checkNotAPiece($startLocation);
            $curPiece = $this->grid[(string)$startLocation];
            $this->checkTurn($curPiece);

            $opponentId = 1 - $this->curPlayer;

            $curPiece->move($targetLocation, $this->grid, $this->players[$opponentId], $transformTo);

            if ($this->isMate($opponentId)) {
                $this->curGameStatus = ($this->curPlayer == Player::WHITE) ? self::WHITES_WIN : self::BLACKS_WIN;
                return $this->__toString();
            }

            if ($this->isDraw()) {
                $this->curGameStatus = self::DRAW;
                return $this->__toString();
            }

            $this->curPlayer = $opponentId;

            return (string)$this->grid;
        } catch (Exception $exception) {
            return (string)$exception;
        }

    }

    /**
     * @param Piece $curPiece
     * @throws AnotherPieceOwnerException
     */
    private function checkTurn(Piece $curPiece) {
        if (!Piece::isEmpty($curPiece) && $curPiece->getPlayer() !== $this->players[$this->curPlayer]) {
            throw new AnotherPieceOwnerException($curPiece, $this->players[$this->curPlayer]);
        }
    }

    /**
     * @param Location $location
     * @throws NotAPieceException
     */
    private function checkNotAPiece(Location $location): void {
        $piece = $this->grid[(string)$location];
        if (Piece::isEmpty($piece)) {
            throw new NotAPieceException($location);
        }
    }

    /**
     * @param int $opponentId
     * @return bool
     */
    private function isMate(int $opponentId): bool {
        $opponent = $this->players[$opponentId];
        foreach ($opponent->getPieces() as $piece) {
            $oldLocation = $piece->getLocation();
            foreach ($this->grid->getSquares() as $targetLocationString => $_) {
                $targetLocation = Location::getInstanceFromString($targetLocationString);
                $targetPiece = $this->grid[(string)$targetLocation];
                $wasMoved = $piece->isMoved();
                try {
                    $piece->move($targetLocation, $this->grid, $this->players[$this->curPlayer], Pawn::ID);

                    // here is a way out, not a mate, undo changes
                    $piece->undoMove($oldLocation, $targetLocation, $targetPiece, $wasMoved, $this->grid, $this->players[$this->curPlayer]);
                    return false;
                } catch (Exception $ignored) {
                    // ignore
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isDraw(): bool {
        foreach ($this->players as $player) {
            $knightCount = 0;
            $whiteSquareBishopCount = 0;
            $blackSquareBishopCount = 0;
            foreach ($player->getPieces() as $piece) {
                if (!($piece instanceof King || $piece instanceof Knight || $piece instanceof Bishop)) {
                    return false;
                }

                if ($piece instanceof Knight) {
                    ++$knightCount;
                } else if ($piece instanceof Bishop) {
                    $location = $piece->getLocation();
                    if (Grid::squareColorFromLocation($location) == (string)Player::WHITE) {
                        ++$whiteSquareBishopCount;
                    } else {
                        ++$blackSquareBishopCount;
                    }
                }
            }

            if ($knightCount > 1) {
                return false;
            }

            if ($knightCount == 1 && ($whiteSquareBishopCount + $blackSquareBishopCount) > 0) {
                return false;
            }

            if ($whiteSquareBishopCount > 0 && $blackSquareBishopCount > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isEnded(): bool {
        return $this->curGameStatus != GameState::CONTINUING;
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->curGameStatus . PHP_EOL . $this->grid;
    }

}