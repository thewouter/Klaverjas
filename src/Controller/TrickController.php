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
use function Clue\StreamFilter\fun;
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


        // First trick is always correct
        if ($trick->getNextPlayer() > 0) {
            $requested_suit = $trick->getCard1()->getSuit();
            $suit = $card->getSuit();
            $trump_played = $suit == $trick->getGame()->getTrump();
            $trump = $trick->getGame()->getTrump();
            $suit_followed = $suit == $requested_suit;
            $int_rank_trump = array_search($card->getRank(), Trick::$TRUMP_ORDER);
            $played_cards = array_filter([
                    $trick->getCard1(),
                    $trick->getCard2(),
                    $trick->getCard3(),
                    $trick->getCard4(),
                ], function ($c) {
                return !is_null($c);
            });
            $previous_trumps = array_filter($played_cards, function ($c) use ($trump) {
                return $c->getSuit() == $trump;
            });
            $int_previous_trumps = array_map(function ($c) {
                return array_search($c->getRank(), Trick::$TRUMP_ORDER);
            }, $previous_trumps);
            $under_played = max(array_merge($int_previous_trumps, [0])) > $int_rank_trump;
            $trump_in_hand = array_filter($player->getCards()->toArray(), function ($c) use ($trump) {
                return $c->getSuit() == $trump;
            });
            $num_trump_in_hand = count($trump_in_hand);
            $not_trump_in_hand = array_filter($player->getCards()->toArray(), function ($c) use ($trump) {
                return $c->getSuit() != $trump;
            });
            $num_not_trump_in_hand = count($not_trump_in_hand);
            $int_trumps_in_hand = array_map(function ($c) {
                return array_search($c->getRank(), Trick::$TRUMP_ORDER);
            }, $trump_in_hand);
            $undertrump_allowed = max(array_merge($int_trumps_in_hand, [0])) < max(array_merge($int_previous_trumps,[0]));
            $available_of_suit = count(array_filter($player->getCards()->toArray(), function ($c) use ($requested_suit) {
                return $c->getSuit() == $requested_suit;
            }));

            $failed = false;
            $failed_reason = '';

            if ($suit_followed) {
                if ($trump_played) {
                    if ($under_played) {
                        if ($undertrump_allowed) {
                            // OK
                        } else {
                            $failed = true;
                            $failed_reason = 'undertrump not allowed when overtrump possible';
                        }
                    } else {
                        // OK
                    }
                } else {
                    // OK
                }
            } else {
                if($available_of_suit > 0) {
                    $failed = true;
                    $failed_reason = 'correct suit available';
                } else {
                    if ($trump_played) {
                        if ($under_played){
                            if ($undertrump_allowed && $num_not_trump_in_hand == 0) {
                                // OK
                            } else {
                                $failed = true;
                                $failed_reason = 'undertrump not allowed when overtrump possible';
                            }
                        } else {
                            // OK
                        }
                    } else {
                        if ($num_trump_in_hand > 0) {
                            $failed = true;
                            $failed_reason = 'trumping is obligated';
                        } else {
                            // OK
                        }
                    }
                }
            }

            if ($failed) {
                return new JsonResponse([
                    'status' => 'FAILED',
                    'reason' => $failed_reason
                ], Response::HTTP_BAD_REQUEST);
            }
        }





        $trick->setNextCard($card, $player);
        $player->removeCard($card);

        if ($trick->getCurrentPlayer() === false) { // Trick done

            if ($trick->getPlayer1()->getCards()->count() == 0 ||
                $trick->getPlayer2()->getCards()->count() == 0 ||
                $trick->getPlayer3()->getCards()->count() == 0 ||
                $trick->getPlayer4()->getCards()->count() == 0 ) { // Complete hand played, deal new hand and update points.

                $game = $trick->getGame();
                $add_points = [0, 0, 0, 0];
                $one_three_us = 0;

                foreach ($game->getTricks() as $tr){
                    $winner = $tr->getWinner();
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

                    if($tr->getCard1()->getSuit() == $game->getTrump()) {
                        $points += $tr->getCard1()->getPointsTrump();
                    } else {
                        $points += $tr->getCard1()->getPoints();
                    }

                    if($tr->getCard2()->getSuit() == $game->getTrump()) {
                        $points += $tr->getCard2()->getPointsTrump();
                    } else {
                        $points += $tr->getCard2()->getPoints();
                    }

                    if($tr->getCard3()->getSuit() == $game->getTrump()) {
                        $points += $tr->getCard3()->getPointsTrump();
                    } else {
                        $points += $tr->getCard3()->getPoints();
                    }

                    if($tr->getCard4()->getSuit() == $game->getTrump()) {
                        $points += $tr->getCard4()->getPointsTrump();
                    } else {
                        $points += $tr->getCard4()->getPoints();
                    }

                    $windex = (($one_three_us? 0 : 1) + $winner) % 2;
                    $add_points[$windex] += $points;

                    $meld = $tr->getMeld();
                    $add_points[$windex + 2] += $meld;

                    $entityManager->remove($tr);
                }

                $last_trick_points = $game->getTricks()->last()->getWinner() % 2;
                $add_points[(($one_three_us? 0 : 1) + $last_trick_points) % 2] += 10;

                $playing_side = (array_search(true, $game->getTrumpChosen()) + $game->getChair()) % 2;
                if ($add_points[$playing_side] + $add_points[$playing_side + 2] <= $add_points[1-$playing_side] + $add_points[1-$playing_side + 2]) { // Wet
                    $add_points[1-$playing_side] = 162; //All points to other team
                    $add_points[2 + 1 - $playing_side] += $add_points[2 + $playing_side]; // Meld to other team
                    $add_points[$playing_side] = 0;
                    $add_points[2 + $playing_side] = 0;
                }

                $game->addPoints($add_points);
                $firstTrick = $game->getTricks()->first();
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

                $players = [
                    $firstTrick->getPlayer1(),
                    $firstTrick->getPlayer2(),
                    $firstTrick->getPlayer3(),
                    $firstTrick->getPlayer4(),
                ];
                $newTrick = new Trick();
                $newTrick->setPlayer1($players[1]);
                $newTrick->setPlayer2($players[2]);
                $newTrick->setPlayer3($players[3]);
                $newTrick->setPlayer4($players[0]);
                $game->addTrick($newTrick);

                $game ->resetTrump();

            } else {
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
            }

            $entityManager->persist($newTrick);
            $entityManager->flush();
//            first player can be changed so force update of game at clients
            $sender->sendUpdate($trick->getGame()->getClassName(), MercureSender::METHOD_PATCH, $trick->getGame()->toArray());

        }

        $entityManager->flush();

        return new JsonResponse($trick->getGame()->toArray(), Response::HTTP_OK);
    }
}
