<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CardRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/{trick}", name="trick_get", methods={"GET"})
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get_trick(Trick $trick)
    {
        return new JsonResponse($trick->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/{trick}/play", name="trick_play", methods={"POST"})
     * @param Request $request
     * @param PlayerRepository $clientRepository
     * @param CardRepository $cardRepository
     * @param EntityManagerInterface $entityManager
     * @param Trick $trick
     * @return JsonResponse
     */
    public function playCard(Request $request, PlayerRepository $clientRepository, CardRepository $cardRepository, EntityManagerInterface $entityManager, Trick $trick){
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

        $card = $cardRepository->find($data['card']);

        if(is_null($card)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Card not found',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($card, $client->getCards()->toArray())){
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Card not playable by User',
            ], Response::HTTP_BAD_REQUEST);
        }

        if(!$trick->getGame()->getRoom()->hasClient($client)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'User not in the game of this trick',
            ], Response::HTTP_BAD_REQUEST);
        }

        $currentPlayer = $trick->getCurrentPlayer();

        if (is_null($currentPlayer)) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Trick already completed',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($currentPlayer->getId() != $client->getId()) {
            return new JsonResponse([
                'status' => 'FAILED',
                'message' => 'Not this players turn',
            ], Response::HTTP_BAD_REQUEST);
        }

        $trick->setNextCard($card, $client);
        $client->removeCard($card);

        $entityManager->flush();


        return new JsonResponse($trick->getGame()->toArray(), Response::HTTP_BAD_REQUEST);
    }
}
