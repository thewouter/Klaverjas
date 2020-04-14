<?php

namespace App\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\CardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

/**
 * Class CardController
 * @package App\Controller
 * @Route("/card")
 */
class CardController extends AbstractController
{
    /**
     * @Route("/setup", name="card_setup", methods={"POST"})
     */
    public function setup(EntityManagerInterface $entityManager, CardRepository $repository) {
        $cards = $repository->findAll();
        foreach ($cards as $card) {
            $entityManager->remove($card);
        }

        AppFixtures::setupCards($entityManager);
        $entityManager->flush();

        return new JsonResponse(['status'=> 'OK'], Response::HTTP_OK);
    }
}
