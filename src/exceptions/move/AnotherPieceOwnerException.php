<?php
declare(strict_types = 1);

namespace App\exceptions\move;

use App\Entity\piece\Piece;
use App\Entity\player\Player;

/**
 * Class AnotherPieceOwnerException
 * @package App\exceptions\move
 */
class AnotherPieceOwnerException extends InvalidMoveException {

    /**
     * @var Piece
     */
    private Piece $piece;
    /**
     * @var Player
     */
    private Player $curPlayer;

    /**
     * UnknownPieceException constructor.
     * @param Piece $piece
     * @param Player $curPlayer
     */
    public function __construct(Piece $piece, Player $curPlayer) {
        $this->piece = $piece;
        $this->curPlayer = $curPlayer;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        $pieceLocation = $this->piece->getLocation();
        $pieceOwner = $this->piece->getPlayer();
        return "Piece $this->piece at $pieceLocation is owned by $pieceOwner, not $this->curPlayer";
    }
}