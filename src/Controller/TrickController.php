<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Trick;
use App\Repository\CardRepository;
use App\Repository\ClientRepository;
use App\Repository\PlayerRepository;
use App\Utility\MercureSender;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Sodium\add;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/{trick}", name="trick_get", methods={"GET"})
     * @param Trick $trick
     * @return Response
     */
    public function get_trick(Trick $trick)
    {
        return new JsonResponse($trick->toArray(), Response::HTTP_OK);
    }


    /**
     * @Route("/{trick}/play", name="trick_play", methods={"POST"})
     * @param Request $request
     * @param ClientRepository $clientRepository
     * @param PlayerRepository $playerRepository
     * @param CardRepository $cardRepository
     * @param EntityManagerInterface $entityManager
     * @param MercureSender $sender
     * @param Trick $trick
     * @return JsonResponse
     */
    public function playCard(Request $request, ClientRepository $clientRepository, PlayerRepository $playerRepository, CardRepository $cardRepository, EntityManagerInterface $entityManager, MercureSender $sender, Trick $trick){
        $data = json_decode($request->getContent(), true);

        if (!array_key_exists('client', $data)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'No user provided',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!array_key_exists('card', $data)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'No card provided',
            ], Response::HTTP_BAD_REQUEST);
        }

        $client = $clientRepository->find($data['client']);

        if(is_null($client)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'User not found',
            ], Response::HTTP_BAD_REQUEST);
        }

        $player = $playerRepository->find($client->getPlayer());


        $card = $cardRepository->find($data['card']);

        if(is_null($card)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Card not found',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($card, $player->getCards()->toArray())){
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Card not playable by User',
            ], Response::HTTP_BAD_REQUEST);
        }

        if(!$trick->getGame()->getRoom()->hasPlayer($player)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'User not in the game of this trick',
            ], Response::HTTP_BAD_REQUEST);
        }

        if(!$trick->getGame()->getTrumpChosen()) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Trump not yet chosen',
            ], Response::HTTP_BAD_REQUEST);
        }

        $currentPlayer = $trick->getCurrentPlayer();

        if ($currentPlayer === false) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Trick already completed',
            ], Response::HTTP_BAD_REQUEST);
        }



        if ($currentPlayer->getId() != $player->getId()) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Not this players turn',
            ], Response::HTTP_BAD_REQUEST);
        }

        $trick->setNextCard($card, $player);
        $player->removeCard($card);

        if ($trick->getCurrentPlayer() === false) { // Trick done

            if ($trick->getPlayer1()->getCards()->count() == 0 ||
                $trick->getPlayer2()->getCards()->count() == 0 ||
                $trick->getPlayer3()->getCards()->count() == 0 ||
                $trick->getPlayer4()->getCards()->count() == 0 ) { // Complete hand played, deal new hand and update points.

                $game = $trick->getGame();
                $us_points = 0;
                $them_points = 0;
                foreach ($game->getTricks() as $tr){
                    $winner = $trick->getWinner();
                    $one_three_us = true;

                    $first_player = $tr->getPlayer1();
                    switch ($first_player->getId()){
                        case $game->getRoom()->getUs1()->getId():
                        case $game->getRoom()->getUs2()->getId(): // Player 1 and Player 3 are Us
                            $one_three_us = true;
                            break;
                        case $game->getRoom()->getThem1()->getId():
                        case $game->getRoom()->getThem2()->getId(): // Player 2 and Player 4 are Us
                            $one_three_us = false;
                            break;
                    }
                    $points = 0;

                    if($trick->getCard1()->getSuit() == $game->getTrump()) {
                        $points += $trick->getCard1()->getPointsTrump();
                    } else {
                        $points += $trick->getCard1()->getPoints();
                    }

                    if($trick->getCard2()->getSuit() == $game->getTrump()) {
                        $points += $trick->getCard2()->getPointsTrump();
                    } else {
                        $points += $trick->getCard2()->getPoints();
                    }

                    if($trick->getCard3()->getSuit() == $game->getTrump()) {
                        $points += $trick->getCard3()->getPointsTrump();
                    } else {
                        $points += $trick->getCard3()->getPoints();
                    }

                    if($trick->getCard4()->getSuit() == $game->getTrump()) {
                        $points += $trick->getCard4()->getPointsTrump();
                    } else {
                        $points += $trick->getCard4()->getPoints();
                    }

                    $windex = (($one_three_us? 1 : 0) + $winner) % 2;
                    $add_points = [0, 0];
                    $add_points[$windex] += $points;
                    $game->addPoints($add_points);

                    $entityManager->remove($tr);
                }
                $game->addPoints([$us_points, $them_points]); // add points
                $game->setTricks(new ArrayCollection()); // reset hand by removing all tricks

                //        Shuffle cards and deal them to the players
                $cards = $cardRepository->findAll();
                shuffle($cards);
                $game->getRoom()->getUs1()->removeAllCards();
                $game->getRoom()->getUs1()->addCards(array_slice($cards, 0, 8));
                $game->getRoom()->getUs2()->removeAllCards();
                $game->getRoom()->getUs2()->addCards(array_slice($cards, 8, 8));
                $game->getRoom()->getThem1()->removeAllCards();
                $game->getRoom()->getThem1()->addCards(array_slice($cards, 16, 8));
                $game->getRoom()->getThem2()->removeAllCards();
                $game->getRoom()->getThem2()->addCards(array_slice($cards, 24, 8));
            }
            $trick_winner = $trick->getWinner();
            $players = [
                $trick->getPlayer1(),
                $trick->getPlayer2(),
                $trick->getPlayer3(),
                $trick->getPlayer4(),
            ];

            $newTrick = new Trick();
            $newTrick->setPlayer1($players[$trick_winner]);
            $newTrick->setPlayer2($players[($trick_winner + 1) % 4]);
            $newTrick->setPlayer3($players[($trick_winner + 2) % 4]);
            $newTrick->setPlayer4($players[($trick_winner + 3) % 4]);
            $trick->getGame()->addTrick($newTrick);

//            first player can be changed so force update of game at clients

            $sender->sendUpdate($trick->getGame()->getClassName(), MercureSender::METHOD_PATCH, $trick->getGame()->toArray());

            $entityManager->persist($newTrick);
        }

        $entityManager->flush();

        return new JsonResponse($trick->getGame()->toArray(), Response::HTTP_OK);
    }
}
