<?php
declare(strict_types = 1);

namespace App\Tests\api;

use App\Entity\game\GameState;
use App\Entity\grid\Location;

require_once '../../vendor/autoload.php';

$game = GameState::startGame();
$starts = array("e2", "e7", "f1", "b8", "d1", "g8", "h5");
$ends = array("e4", "e5", "c4", "c6", "h5", "f6", "f7");
echo $game->getGrid();
echo "\n";
for ($i = 0; $i < count($starts); ++$i) {
    echo $game->move(Location::getInstanceFromString($starts[$i]), Location::getInstanceFromString($ends[$i]));
    echo PHP_EOL;
}