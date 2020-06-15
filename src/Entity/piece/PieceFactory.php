<?php
declare(strict_types = 1);

namespace App\Entity\piece;

use App\exceptions\piece\UnknownPieceException;

/**
 * Class PieceFactory
 * @package App\Entity\piece
 */
class PieceFactory {
    /**
     * @param $player
     * @param int $id
     * @return Piece
     * @throws UnknownPieceException
     */
    public static function makePiece($player, int $id): Piece {
        switch ($id) {
            case Pawn::ID:
                return new Pawn($player);
            case Knight::ID:
                return new Knight($player);
            case Bishop::ID:
                return new Bishop($player);
            case Rook::ID:
                return new Rook($player);
            case Queen::ID:
                return new Queen($player);
            case King::ID:
                return new King($player);
            default:
                throw new UnknownPieceException($id);
        }
    }
}