<?php

namespace App\Controller;

use App\Entity\game\GameState;
use App\Entity\grid\Location;
use App\Entity\piece\Pawn;
use App\Entity\piece\Queen;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChessController extends AbstractController {
    /**
     * @Route("/start", name="start_game")
     */
    public function startGame(): Response {
        $entityManager = $this->getDoctrine()->getManager();

        $game = GameState::startGame();
        $entityManager->persist($game);
        $entityManager->flush();

        return new Response('Saved new game with id ' . $game->getId() . PHP_EOL . $game);
    }

    /**
     * @Route("/move/{id}", name="move")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function move(int $id, Request $request): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $game = $entityManager->getRepository(GameState::class)->find($id);
        if (is_null($game)) {
            throw $this->createNotFoundException(
                'No game found for id '.$id
            );
        }

        $start = $request->request->get("start");
        $target = $request->request->get("end");
        $transformTo = $request->request->get("transformTo", Queen::ID);

        $result = $game->move(Location::getInstanceFromString($start), Location::getInstanceFromString($target), $transformTo);
        $entityManager->flush();

        return new Response($result);
    }
}
