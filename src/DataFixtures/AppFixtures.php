<?php

namespace App\DataFixtures;

use App\Entity\Card;
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

        $this->setupCards($manager);

        $manager->flush();
    }

    private function setupCards(ObjectManager $manager){
        $h7 = new Card();
        $h7->setRank('7');
        $h7->setSuit('h');
        $manager->persist($h7);
        $h8 = new Card();
        $h8->setRank('8');
        $h8->setSuit('h');
        $manager->persist($h8);
        $h9 = new Card();
        $h9->setRank('9');
        $h9->setSuit('h');
        $manager->persist($h9);
        $ht = new Card();
        $ht->setRank('t');
        $ht->setSuit('h');
        $manager->persist($ht);
        $hj = new Card();
        $hj->setRank('j');
        $hj->setSuit('h');
        $manager->persist($hj);
        $hq = new Card();
        $hq->setRank('q');
        $hq->setSuit('h');
        $manager->persist($hq);
        $hk = new Card();
        $hk->setRank('k');
        $hk->setSuit('h');
        $manager->persist($hk);
        $ha = new Card();
        $ha->setRank('a');
        $ha->setSuit('h');
        $manager->persist($ha);
        $d7 = new Card();
        $d7->setRank('7');
        $d7->setSuit('d');
        $manager->persist($d7);
        $d8 = new Card();
        $d8->setRank('8');
        $d8->setSuit('d');
        $manager->persist($d8);
        $d9 = new Card();
        $d9->setRank('9');
        $d9->setSuit('d');
        $manager->persist($d9);
        $dt = new Card();
        $dt->setRank('t');
        $dt->setSuit('d');
        $manager->persist($dt);
        $dj = new Card();
        $dj->setRank('j');
        $dj->setSuit('d');
        $manager->persist($dj);
        $dq = new Card();
        $dq->setRank('q');
        $dq->setSuit('d');
        $manager->persist($dq);
        $dk = new Card();
        $dk->setRank('k');
        $dk->setSuit('d');
        $manager->persist($dk);
        $da = new Card();
        $da->setRank('a');
        $da->setSuit('d');
        $manager->persist($da);
        $s7 = new Card();
        $s7->setRank('7');
        $s7->setSuit('s');
        $manager->persist($s7);
        $s8 = new Card();
        $s8->setRank('8');
        $s8->setSuit('s');
        $manager->persist($s8);
        $s9 = new Card();
        $s9->setRank('9');
        $s9->setSuit('s');
        $manager->persist($s9);
        $st = new Card();
        $st->setRank('t');
        $st->setSuit('s');
        $manager->persist($st);
        $sj = new Card();
        $sj->setRank('j');
        $sj->setSuit('s');
        $manager->persist($sj);
        $sq = new Card();
        $sq->setRank('q');
        $sq->setSuit('s');
        $manager->persist($sq);
        $sk = new Card();
        $sk->setRank('k');
        $sk->setSuit('s');
        $manager->persist($sk);
        $sa = new Card();
        $sa->setRank('a');
        $sa->setSuit('s');
        $manager->persist($sa);
        $c7 = new Card();
        $c7->setRank('7');
        $c7->setSuit('c');
        $manager->persist($c7);
        $c8 = new Card();
        $c8->setRank('8');
        $c8->setSuit('c');
        $manager->persist($c8);
        $c9 = new Card();
        $c9->setRank('9');
        $c9->setSuit('c');
        $manager->persist($c9);
        $ct = new Card();
        $ct->setRank('t');
        $ct->setSuit('c');
        $manager->persist($ct);
        $cj = new Card();
        $cj->setRank('j');
        $cj->setSuit('c');
        $manager->persist($cj);
        $cq = new Card();
        $cq->setRank('q');
        $cq->setSuit('c');
        $manager->persist($cq);
        $ck = new Card();
        $ck->setRank('k');
        $ck->setSuit('c');
        $manager->persist($ck);
        $ca = new Card();
        $ca->setRank('a');
        $ca->setSuit('c');
        $manager->persist($ca);
    }
}
