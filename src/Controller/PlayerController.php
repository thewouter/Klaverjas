<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Game;
use App\Entity\Room;
use App\Repository\ClientRepository;
use App\Repository\PlayerRepository;
use App\Repository\RoomRepository;
use App\Utility\MercureSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use function Clue\StreamFilter\remove;

/**
 * @Route("/player")
 */
class PlayerController extends AbstractController
{
    private $sender;

    public function __construct(MercureSender $sender) {
        $this->sender = $sender;
    }

    /**
     * @Route("/{player}", name="player_get", methods={"GET"})
     * @param RoomRepository $repository
     * @param Player $player
     * @return object|void
     */
    public function get_game(RoomRepository $repository, Player $player) {
        if (is_null($player)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($player->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{player}", name="player_update", methods={"PATCH"})
     * @param Request $request
     * @param ClientRepository $repository
     * @param PlayerRepository $playerRepository
     * @param EntityManagerInterface $entityManager
     * @param Player $player
     * @return object|void
     */
    public function update(Request $request, ClientRepository $repository, PlayerRepository $playerRepository, EntityManagerInterface $entityManager, Player $player) {
        $data = json_decode($request->getContent(), true);
        $client = $data['client'];

        $client = $repository->find($client);

        if(!is_null($player->getClient())) {
            return new JsonResponse(['status' => 'FAILED', 'reason' => 'already client at this seat'], Response::HTTP_BAD_REQUEST);
        }

        if (!empty($client)){
            RoomController::removeClientFromAllPlayers($client, $entityManager, $playerRepository);
            $player->setClient($client);
        }


        $entityManager->flush();

        return new JsonResponse($player->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{player}", name="player_delete", methods={"DELETE"})
     * @param RoomRepository $repository
     * @param EntityManagerInterface $entityManager
     * @param Player $player
     * @return object|void
     */
    public function delete(RoomRepository $repository, EntityManagerInterface $entityManager, Player $player) {
        if (is_null($player)) {
            return new Response("Not Found", Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($player);
        $entityManager->flush();

        $room = $repository->findOneByPlayer($player);



        return new JsonResponse([
            'status' => 'OK'
        ], Response::HTTP_OK);
    }
}
