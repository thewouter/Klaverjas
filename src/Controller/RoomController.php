<?php

namespace App\Controller;

use App\Entity\Client;
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
     * @Route("/{room}", name="room_get", methods={"GET"})
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
     * @Route("/{room}", name="room_delete", methods={"DELETE"})
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

    /**
     * @Route("/{room}", name="room_update", methods={"PATCH"})
     * @param Request $request
     * @param ClientRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Room $room
     * @return object|void
     */
    public function update(Request $request, ClientRepository $repository, EntityManagerInterface $entityManager, Room $room) {
        if (is_null($room)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (array_key_exists('name', $data)){
            $name = $data['name'];
            $room->setName($name);
        }
        if (array_key_exists('us1', $data)){
            $us1 = $repository->find($data['us1']);
            if($us1->getRoom()) {
                $us1->getRoom()->removeClient($us1);
                $entityManager->flush();
            }
            if(!is_null($us1)){
                $room->setUs1($us1);
            }
        }
        if (array_key_exists('us2', $data)){
            $us2 = $repository->find($data['us2']);
            if($us2->getRoom()) {
                $us2->getRoom()->removeClient($us2);
                $entityManager->flush();
            }
            if(!is_null($us2)){
                $room->setUs2($us2);
            }
        }
        if (array_key_exists('them1', $data)){
            $them1 = $repository->find($data['them1']);
            if($them1->getRoom()) {
                $them1->getRoom()->removeClient($them1);
                $entityManager->flush();
            }
            if(!is_null($them1)){
                $room->setThem1($them1);
            }
        }
        if (array_key_exists('them2', $data)){
            $them2 = $repository->find($data['them2']);
            if($them2->getRoom()) {
                $them2->getRoom()->removeClient($them2);
                $entityManager->flush();
            }
            if(!is_null($them2)){
                $room->setThem2($them2);
            }
        }
        $entityManager->flush();

        return new JsonResponse($room->toArray(), Response::HTTP_OK);
    }
}
