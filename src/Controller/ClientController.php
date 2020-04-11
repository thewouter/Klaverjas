<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\ClientRepository;
use App\Repository\PlayerRepository;
use App\Repository\RoomRepository;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClientController
 * @package App\Controller
 * @Route("/client")
 */
class ClientController extends AbstractController {

    /**
     * @Route("/add", name="player_add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ClientRepository $repository
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $em, ClientRepository $repository) {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        if(empty($name)) {
            return new Response("Not all parameters have been provided", Response::HTTP_BAD_REQUEST);
        }

        $exists = $repository->findBy(['name' => $name]);

        if(!empty($exists)) {
            return new JsonResponse($exists[0]->toArray(), Response::HTTP_FOUND);
        }

        $client = new Client();
        $client->setName($name);
        $em->persist($client);
        $em->flush();

        return new JsonResponse($client->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/{client}/logout", name="player_logout")
     * @param Client $client
     * @param RoomRepository $roomRepository
     * @param EntityManagerInterface $entityManager
     */
    public function logout(Client $client, RoomRepository $roomRepository, EntityManagerInterface $entityManager){
        RoomController::removeClientFromAllRooms($client, $entityManager, $roomRepository);
    }
}
