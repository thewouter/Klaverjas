<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Game;
use App\Entity\Room;
use App\Repository\PlayerRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use function Clue\StreamFilter\remove;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/add", name="client_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em, PlayerRepository $repository) {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        if(empty($name)) {
            return new Response("Not all parameters have been provided", Response::HTTP_BAD_REQUEST);
        }

        $exists = $repository->findBy(['name' => $name]);

        if(!empty($exists)) {
            return new JsonResponse($exists[0]->toArray(), Response::HTTP_FOUND);
        }

        $client = new Player();
        $client->setName($name);
        $em->persist($client);
        $em->flush();

        return new JsonResponse($client->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{client}", name="client_get", methods={"GET"})
     * @param RoomRepository $repository
     * @param Player $client
     * @return object|void
     */
    public function get_game(RoomRepository $repository, Player $client) {
        if (is_null($client)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($client->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{client}", name="client_update", methods={"PATCH"})
     * @param Request $request
     * @param RoomRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Player $client
     * @return object|void
     */
    public function update(Request $request, RoomRepository $repository, EntityManagerInterface $entityManager, Player $client) {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        if (is_null($client)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        if (!empty($name)){
            $client->setName($name);
        }

        $client->setName($name);
        $entityManager->flush();

        return new JsonResponse($client->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{client}/logout", name="client_logout")
     * @param Player $client
     * @param RoomRepository $roomRepository
     * @param EntityManagerInterface $entityManager
     */
    public function logout(Player $client, RoomRepository $roomRepository, EntityManagerInterface $entityManager){
        RoomController::removeClientFromAllRooms($client, $entityManager, $roomRepository);
    }

    /**
     * @Route("/{client}", name="client_delete", methods={"DELETE"})
     * @param RoomRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Player $client
     * @return object|void
     */
    public function delete(RoomRepository $repository, EntityManagerInterface $entityManager, Player $client) {
        if (is_null($client)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($client);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);
    }
}
