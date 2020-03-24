<?php

namespace App\Controller;

use App\Entity\Room;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends AbstractController
{
    /**
     * @Route("/room", name="room")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RoomController.php',
        ]);
    }

    /**
     * @Route("/add", name="room_add", methods={"POST"})
     * @param Request $request
     * @param EntityManager $em
     * @return JsonResponse
     */
    public function add(Request $request, EntityManager $em) {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];

        $room = new Room();
        $room->setName($name);
        $em->persist($room);
        $em->flush();

        return new JsonResponse([
            'status' => 'OK'
        ]);
    }
}
