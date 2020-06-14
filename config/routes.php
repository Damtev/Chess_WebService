<?php
declare(strict_types = 1);

use App\Controller\ChessController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('start_game', '/start')
        ->controller([ChessController::class, 'startGame'])
        ->methods(['GET'])
    ;
    $routes->add('move', '/move/{id}')
        ->controller([ChessController::class, 'move'])
        ->requirements(['id' => '\d+'])
        ->methods(['PUT'])
    ;
};