<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Game;
use App\Entity\Room;
use App\Repository\ClientRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/add", name="room_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em) {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        if(empty($name)) {
            return new Response("Not all parameters have been provided", Response::HTTP_BAD_REQUEST);
        }

        $room = new Room();
        $room->setName($name);
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

        $entityManager->remove($room);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);
    }

    public static function removeClientFromAllRooms(?Client $client, EntityManagerInterface $entityManager, RoomRepository $roomRepository) {
        foreach ($roomRepository->findBy(['us1' => $client]) as $room) {
            $room->setUs1(null);
        }
        foreach ($roomRepository->findBy(['us2' => $client]) as $room) {
            $room->setUs2(null);
        }
        foreach ($roomRepository->findBy(['them1' => $client]) as $room) {
            $room->setThem1(null);
        }
        foreach ($roomRepository->findBy(['them2' => $client]) as $room) {
            $room->setThem2(null);
        }
        $entityManager->flush();
    }

    /**
     * @Route("/{room}", name="room_update", methods={"PATCH"}, requirements={"room"="\d+"})
     * @param Request $request
     * @param ClientRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Room $room
     * @return object|void
     */
    public function update(Request $request, ClientRepository $repository, EntityManagerInterface $entityManager, Room $room, RoomRepository $roomRepository) {
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
                $this->removeClientFromAllRooms($room->getUs1(), $entityManager, $roomRepository);
            }
            $us1 = $repository->find($data['us1']);
            if(!is_null($room->getUs1())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllRooms($us1, $entityManager, $roomRepository);
            if(!is_null($us1)){
                $room->setUs1($us1);
            }
        }
        if (array_key_exists('us2', $data)){
            if ($data['us2'] === false){
                $this->removeClientFromAllRooms($room->getUs2(), $entityManager, $roomRepository);
            }
            $us2 = $repository->find($data['us2']);
            if(!is_null($room->getUs2())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllRooms($us2, $entityManager, $roomRepository);
            if(!is_null($us2)){
                $room->setUs2($us2);
            }
        }
        if (array_key_exists('them1', $data)){
            if ($data['them1'] === false){
                $this->removeClientFromAllRooms($room->getThem1(), $entityManager, $roomRepository);
            }
            $them1 = $repository->find($data['them1']);
            if(!is_null($room->getThem1())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllRooms($them1, $entityManager, $roomRepository);
            if(!is_null($them1)){
                $room->setThem1($them1);
            }
        }
        if (array_key_exists('them2', $data)){
            if ($data['them2'] === false){
                $this->removeClientFromAllRooms($room->getThem2(), $entityManager, $roomRepository);
            }
            $them2 = $repository->find($data['them2']);
            if(!is_null($room->getThem2())){
                return new JsonResponse($room->toArray(), Response::HTTP_OK);
            }
            $this->removeClientFromAllRooms($them2, $entityManager, $roomRepository);
            if(!is_null($them2)){
                $room->setThem2($them2);
            }
        }
        $entityManager->flush();

        if($room->isFull()) {
            $game = new Game();
            $room->addGame($game);

        }

        return new JsonResponse($room->toArray(), Response::HTTP_OK);
    }
}
