<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Game;
use App\Entity\Room;
use App\Repository\PlayerRepository;
use App\Repository\RoomRepository;
use App\Utility\MercureSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\Update;

/**
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/add", name="room_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param PublisherInterface $publisher
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em, PublisherInterface $publisher) {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        if(empty($name)) {
            return new Response("Not all parameters have been provided", Response::HTTP_BAD_REQUEST);
        }

        $room = new Room();
        $room->setName($name);
        $game = new Game();
        $room->addGame($game);
        $em->persist($game);
        $em->persist($room);
        $em->flush();


        return new JsonResponse($room->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{room}", name="room_get", methods={"GET"}, requirements={"room"="\d+"})
     * @param RoomRepository $repository
     * @param Room $room
     * @return object|void
     */
    public function get_room(RoomRepository $repository, Room $room) {
        if (is_null($room)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($room->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/list", name="room_list", methods={"GET"})
     * @param RoomRepository $repository
     * @return JsonResponse
     */
    public function list(RoomRepository $repository) {
        $rooms = $repository->findAll();
        return new JsonResponse(array_map(function ($room) {
            return $room->toArray();
        }, $rooms), Response::HTTP_OK);
    }

    /**
     * @Route("/{room}", name="room_delete", methods={"DELETE"}, requirements={"room"="\d+"})
     * @param RoomRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Room $room
     * @return object|void
     */
    public function delete(RoomRepository $repository, EntityManagerInterface $entityManager, Room $room) {
        if (is_null($room)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        if (!is_null($room->getUs1()->getClient()) ||
            !is_null($room->getUs2()->getClient()) ||
            !is_null($room->getThem1()->getClient()) ||
            !is_null($room->getThem2()->getClient())) {
            return new JsonResponse([
                'status' => 'FAILED',
                'reason' => 'Room not empty',
            ], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->remove($room);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);
    }

    public static function removeClientFromAllPlayers(?Client $client, EntityManagerInterface $entityManager, PlayerRepository $playerRepository) {
        foreach ($playerRepository->findBy(['client' => $client]) as $player) {
            $player->setClient(null);
            $entityManager->flush();
        }
    }

    /**
     * @Route("/{room}", name="room_update", methods={"PATCH"}, requirements={"room"="\d+"})
     * @param Request $request
     * @param PlayerRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Room $room
     * @param PlayerRepository $roomRepository
     * @return object|void
     */
    public function update(Request $request, PlayerRepository $repository, EntityManagerInterface $entityManager, Room $room, PlayerRepository $roomRepository) {
        if (is_null($room)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (array_key_exists('name', $data)){
            $name = $data['name'];
            $room->setName($name);
        }
        if (array_key_exists('us1', $data)){
            if ($data['us1'] === false){
                $this->removeClientFromAllPlayers($room->getUs1()->getClient(), $entityManager, $roomRepository);
            }
            $us1 = $repository->find($data['us1']);
            if(!is_null($room->getUs1())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllPlayers($us1->getClient(), $entityManager, $roomRepository);
            if(!is_null($us1)){
                $room->setUs1($us1);
            }
        }
        if (array_key_exists('us2', $data)){
            if ($data['us2'] === false){
                $this->removeClientFromAllPlayers($room->getUs2()->getClient(), $entityManager, $roomRepository);
            }
            $us2 = $repository->find($data['us2']);
            if(!is_null($room->getUs2())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllPlayers($us2->getClient(), $entityManager, $roomRepository);
            if(!is_null($us2)){
                $room->setUs2($us2);
            }
        }
        if (array_key_exists('them1', $data)){
            if ($data['them1'] === false){
                $this->removeClientFromAllPlayers($room->getThem1()->getClient(), $entityManager, $roomRepository);
            }
            $them1 = $repository->find($data['them1']);
            if(!is_null($room->getThem1())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllPlayers($them1->getClient(), $entityManager, $roomRepository);
            if(!is_null($them1)){
                $room->setThem1($them1);
            }
        }
        if (array_key_exists('them2', $data)){
            if ($data['them2'] === false){
                $this->removeClientFromAllPlayers($room->getThem2()->getClient(), $entityManager, $roomRepository);
            }
            $them2 = $repository->find($data['them2']);
            if(!is_null($room->getThem2())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllPlayers($them2->getClient(), $entityManager, $roomRepository);
            if(!is_null($them2)){
                $room->setThem2($them2);
            }
        }
        $entityManager->flush();

//        if($room->isFull()) {
//            $game = new Game();
//            $room->addGame($game);
//
//        }

        return new JsonResponse($room->toArray(), Response::HTTP_OK);
    }
}
