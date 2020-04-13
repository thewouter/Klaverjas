<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Game;
use App\Entity\Room;
use App\Entity\Trick;
use App\Repository\CardRepository;
use App\Repository\PlayerRepository;
use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/add", name="game_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em) {
        $data = json_decode($request->getContent(), true);
        $room = $data['room'];

        if(empty($room)) {
            return new Response("Not all parameters have been provided", Response::HTTP_BAD_REQUEST);
        }
        $room = $this->getDoctrine()->getRepository(Room::class)->findOneBy(['id' => $room]);
        if(empty($room)) {
            return new Response("Room not found", Response::HTTP_NOT_FOUND);
        }
        $game = new Game();
        $game->setRoom($room);
        $em->persist($game);
        $em->flush();

        return new JsonResponse($game->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{game}", name="game_get", methods={"GET"})
     * @param RoomRepository $repository
     * @param Game $game
     * @return object|void
     */
    public function get_game(RoomRepository $repository, Game $game) {
        if (is_null($game)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($game->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{game}", name="game_delete", methods={"DELETE"})
     * @param RoomRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Game $game
     * @return object|void
     */
    public function delete(RoomRepository $repository, EntityManagerInterface $entityManager, Game $game) {
        if (is_null($game)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($game);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);
    }


    /**
     * @Route("/{game}", name="game_update", methods={"PATCH"})
     * @param Request $request
     * @param PlayerRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Game $game
     * @return object|void
     */
    public function update(Request $request, PlayerRepository $repository, EntityManagerInterface $entityManager, Game $game) {
        if (is_null($game)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (array_key_exists('points', $data)){
            $points = $data['points'];
            $game->setPoints($points);
        }
        if (array_key_exists('room', $data)){
            return new JsonResponse([
                'status' => "FAILED",
                'message' => "Cannot move to another room",
            ]);
        }

        if (array_key_exists('trump_chosen', $data)){
            $yes_no = $data['trump_chosen'];
            $game->setTrumpChosen($yes_no);
        }

        if (array_key_exists('trump', $data)){
            $trump= $data['trump'];
            $game->setTrump($trump);
        }
        $entityManager->flush();

        return new JsonResponse($game->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{game}/start", name="game_deal", methods={"PATCH"})
     * @param Game $game
     * @param CardRepository $cardRepository
     * @return JsonResponse
     */
    public function start(Game $game, CardRepository $cardRepository, EntityManagerInterface $entityManager){
        $room = $game->getRoom();
        $us1 = $room->getUs1();
        $us2 = $room->getUs2();
        $them1 = $room->getThem1();
        $them2 = $room->getThem2();

        if(!$room->isFull()) {
            return new JsonResponse([
                'status' => "FAILED",
                'message' => "Room is not full yet",
            ], Response::HTTP_BAD_REQUEST);
        }

//        Shuffle cards and deal them to the players
        $cards = $cardRepository->findAll();
        shuffle($cards);

        $us1->removeAllCards();
        $us1->addCards(array_slice($cards, 0, 8));
        $us2->removeAllCards();
        $us2->addCards(array_slice($cards, 8, 8));
        $them1->removeAllCards();
        $them1->addCards(array_slice($cards, 16, 8));
        $them2->removeAllCards();
        $them2->addCards(array_slice($cards, 24, 8));

        $game->setTricks(new ArrayCollection());


//        Initialize the game by adding a new first trick
        $trick = new Trick();
        $trick->setPlayer1($us1);
        $trick->setPlayer2($them1);
        $trick->setPlayer3($us2);
        $trick->setPlayer4($them2);
        $entityManager->persist($trick);

        $game->addTrick($trick);

        $game->resetTrump();

        $game->getRoom()->setInGame(true);

        $entityManager->flush();

        return new JsonResponse($game->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{game}/reset/{state}", name="game_reset", methods={"POST"})
     * @param Game $game
     * @param CardRepository $cardRepository
     * @param EntityManagerInterface $entityManager
     * @param int $state
     * @return JsonResponse
     */
    public function reset(Game $game, CardRepository $cardRepository, EntityManagerInterface $entityManager, int $state) {
        if($state == 1) {
            $game->setTricks(new ArrayCollection()); // reset hand by removing all tricks
            $game->getRoom()->setInGame(true);

            $cards = $cardRepository->findAll();
            dump($cards);
            shuffle($cards);
            $game->getRoom()->getUs1()->removeAllCards();
            $game->getRoom()->getUs1()->addCards(array_slice($cards, 0, 8));
            $game->getRoom()->getUs2()->removeAllCards();
            $game->getRoom()->getUs2()->addCards(array_slice($cards, 8, 8));
            $game->getRoom()->getThem1()->removeAllCards();
            $game->getRoom()->getThem1()->addCards(array_slice($cards, 16, 8));
            $game->getRoom()->getThem2()->removeAllCards();
            $game->getRoom()->getThem2()->addCards(array_slice($cards, 24, 8));

            $newTrick = new Trick();
            $newTrick->setPlayer1($game->getRoom()->getUs1());
            $newTrick->setPlayer2($game->getRoom()->getThem1());
            $newTrick->setPlayer3($game->getRoom()->getUs2());
            $newTrick->setPlayer4($game->getRoom()->getThem2());

            $game->resetTrump();

            $game->addTrick($newTrick);

            $game->getRoom()->getUs1()->setClient(null);
            $game->getRoom()->getUs2()->setClient(null);
            $game->getRoom()->getThem1()->setClient(null);
            $game->getRoom()->getThem2()->setClient(null);

            $entityManager->persist($newTrick);
            $entityManager->flush();

            return new JsonResponse($game->toArray(), Response::HTTP_OK);
        }
        if ($state == 2) {
            $game->setTricks(new ArrayCollection()); // reset hand by removing all tricks
            $game->getRoom()->setInGame(false);
            $entityManager->flush();
            return new JsonResponse($game->toArray(), Response::HTTP_OK);
        }
    }
}
