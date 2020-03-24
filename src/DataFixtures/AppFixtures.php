<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Game;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $room = new Room();
            $room->setName($faker->streetName);

            $us1 = new Client();
            $us1->setName($faker->firstName);
            $us2 = new Client();
            $us2->setName($faker->firstName);
            $them1 = new Client();
            $them1->setName($faker->firstName);
            $them2 = new Client();
            $them2->setName($faker->firstName);

            $room->setUs1($us1);
            $room->setUs2($us2);
            $room->setThem1($them1);
            $room->setThem2($them2);

            for($j = 0; $j < 5; $j++) {
                $game = new Game();
                $game->setPoints([$faker->randomDigitNotNull*100, $faker->randomDigitNotNull*100]);
                $room->addGame($game);
            }

            $manager->persist($room);
        }

        $manager->flush();
    }
}
