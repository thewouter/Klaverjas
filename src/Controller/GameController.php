<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Room;
use App\Entity\Trick;
use App\Repository\CardRepository;
use App\Repository\ClientRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $room = $this->getDoctrine()->getRepository(Room::class)->find($room);
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
     * @Route("/{room}", name="room_update", methods={"PATCH"})
     * @param Request $request
     * @param ClientRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Game $game
     * @return object|void
     */
    public function update(Request $request, ClientRepository $repository, EntityManagerInterface $entityManager, Game $game) {
        if (is_null($game)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (array_key_exists('points', $data)){
            $points = $data['points'];
            $game->setPoints($points);
        }
        if (array_key_exists('tricks', $data)){
            $tricks = $data['tricks'];
            $game->setTricks($tricks);
        }
        if (array_key_exists('room', $data)){
            return new JsonResponse([
                'status' => "FAILED",
                'message' => "Cannot move to another room",
            ]);
        }
        $entityManager->flush();

        return new JsonResponse($game->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{game}/deal", name="game_deal", methods={"POST"})
     * @param Game $game
     * @param CardRepository $cardRepository
     * @return JsonResponse
     */
    public function deal(Game $game, CardRepository $cardRepository, EntityManagerInterface $entityManager){
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
        $us2->addCards(array_slice($cards, 8, 16));
        $them1->removeAllCards();
        $them1->addCards(array_slice($cards, 16, 24));
        $them2->removeAllCards();
        $them2->addCards(array_slice($cards, 24, 32));

//        Initialize the game by adding a new first trick

        $trick = new Trick();
        $trick->setPlayer1($us1);
        $trick->setPlayer2($them1);
        $trick->setPlayer3($us2);
        $trick->setPlayer4($them2);
        $entityManager->persist($trick);

        $game->addTrick($trick);

        $entityManager->flush();

        return new JsonResponse($game->toArray(), Response::HTTP_OK);
    }
}
