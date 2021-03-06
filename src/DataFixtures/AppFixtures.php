<?php

namespace App\DataFixtures;

use App\Entity\Card;
use App\Entity\Client;
use App\Entity\Player;
use App\Entity\Game;
use App\Entity\Room;
use App\Entity\Trick;
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
            $room->getUs1()->setClient($us1);
            $us2 = new Client();
            $us2->setName($faker->firstName);
            $room->getUs2()->setClient($us2);
            $them1 = new Client();
            $them1->setName($faker->firstName);
            $room->getThem1()->setClient($them1);
            $them2 = new Client();
            $them2->setName($faker->firstName);
            $room->getThem2()->setClient($them2);

            for($j = 0; $j < 5; $j++) {
                $game = new Game();
                $game->setPoints([[$faker->randomDigitNotNull*100, $faker->randomDigitNotNull*100, 50, 20]]);
                $game->resetTrump();
                $game->setTrumpChosen([false, true, null, null]);
                $room->addGame($game);
                for ($k = 0; $k < 16; $k++) {
                    $trick = new Trick();
                    $trick->setPlayer1($room->getUs1());
                    $trick->setPlayer2($room->getThem1());
                    $trick->setPlayer3($room->getUs2());
                    $trick->setPlayer4($room->getThem2());
                    $trick->setGame($game);
                    $manager->persist($trick);
                }
            }
            $manager->persist($room);
        }

        self::setupCards($manager);

        $manager->flush();
    }

    /**
     * Setup the 32 cards used for klaverjas, Don't worry, generated with python:p
     *
     * @param ObjectManager $manager
     */
    public static function setupCards(ObjectManager $manager){
        $h7 = new Card();
        $h7->setRank('7');
        $h7->setSuit('h');
        $h7->setPointsTrump(0);
        $h7->setPoints(0);
        $manager->persist($h7);
        $h8 = new Card();
        $h8->setRank('8');
        $h8->setSuit('h');
        $h8->setPointsTrump(0);
        $h8->setPoints(0);
        $manager->persist($h8);
        $h9 = new Card();
        $h9->setRank('9');
        $h9->setSuit('h');
        $h9->setPointsTrump(14);
        $h9->setPoints(0);
        $manager->persist($h9);
        $ht = new Card();
        $ht->setRank('t');
        $ht->setSuit('h');
        $ht->setPointsTrump(10);
        $ht->setPoints(10);
        $manager->persist($ht);
        $hj = new Card();
        $hj->setRank('j');
        $hj->setSuit('h');
        $hj->setPointsTrump(20);
        $hj->setPoints(2);
        $manager->persist($hj);
        $hq = new Card();
        $hq->setRank('q');
        $hq->setSuit('h');
        $hq->setPointsTrump(3);
        $hq->setPoints(3);
        $manager->persist($hq);
        $hk = new Card();
        $hk->setRank('k');
        $hk->setSuit('h');
        $hk->setPointsTrump(4);
        $hk->setPoints(4);
        $manager->persist($hk);
        $ha = new Card();
        $ha->setRank('a');
        $ha->setSuit('h');
        $ha->setPointsTrump(11);
        $ha->setPoints(11);
        $manager->persist($ha);
        $d7 = new Card();
        $d7->setRank('7');
        $d7->setSuit('d');
        $d7->setPointsTrump(0);
        $d7->setPoints(0);
        $manager->persist($d7);
        $d8 = new Card();
        $d8->setRank('8');
        $d8->setSuit('d');
        $d8->setPointsTrump(0);
        $d8->setPoints(0);
        $manager->persist($d8);
        $d9 = new Card();
        $d9->setRank('9');
        $d9->setSuit('d');
        $d9->setPointsTrump(14);
        $d9->setPoints(0);
        $manager->persist($d9);
        $dt = new Card();
        $dt->setRank('t');
        $dt->setSuit('d');
        $dt->setPointsTrump(10);
        $dt->setPoints(10);
        $manager->persist($dt);
        $dj = new Card();
        $dj->setRank('j');
        $dj->setSuit('d');
        $dj->setPointsTrump(20);
        $dj->setPoints(2);
        $manager->persist($dj);
        $dq = new Card();
        $dq->setRank('q');
        $dq->setSuit('d');
        $dq->setPointsTrump(3);
        $dq->setPoints(3);
        $manager->persist($dq);
        $dk = new Card();
        $dk->setRank('k');
        $dk->setSuit('d');
        $dk->setPointsTrump(4);
        $dk->setPoints(4);
        $manager->persist($dk);
        $da = new Card();
        $da->setRank('a');
        $da->setSuit('d');
        $da->setPointsTrump(11);
        $da->setPoints(11);
        $manager->persist($da);
        $s7 = new Card();
        $s7->setRank('7');
        $s7->setSuit('s');
        $s7->setPointsTrump(0);
        $s7->setPoints(0);
        $manager->persist($s7);
        $s8 = new Card();
        $s8->setRank('8');
        $s8->setSuit('s');
        $s8->setPointsTrump(0);
        $s8->setPoints(0);
        $manager->persist($s8);
        $s9 = new Card();
        $s9->setRank('9');
        $s9->setSuit('s');
        $s9->setPointsTrump(14);
        $s9->setPoints(0);
        $manager->persist($s9);
        $st = new Card();
        $st->setRank('t');
        $st->setSuit('s');
        $st->setPointsTrump(10);
        $st->setPoints(10);
        $manager->persist($st);
        $sj = new Card();
        $sj->setRank('j');
        $sj->setSuit('s');
        $sj->setPointsTrump(20);
        $sj->setPoints(2);
        $manager->persist($sj);
        $sq = new Card();
        $sq->setRank('q');
        $sq->setSuit('s');
        $sq->setPointsTrump(3);
        $sq->setPoints(3);
        $manager->persist($sq);
        $sk = new Card();
        $sk->setRank('k');
        $sk->setSuit('s');
        $sk->setPointsTrump(4);
        $sk->setPoints(4);
        $manager->persist($sk);
        $sa = new Card();
        $sa->setRank('a');
        $sa->setSuit('s');
        $sa->setPointsTrump(11);
        $sa->setPoints(11);
        $manager->persist($sa);
        $c7 = new Card();
        $c7->setRank('7');
        $c7->setSuit('c');
        $c7->setPointsTrump(0);
        $c7->setPoints(0);
        $manager->persist($c7);
        $c8 = new Card();
        $c8->setRank('8');
        $c8->setSuit('c');
        $c8->setPointsTrump(0);
        $c8->setPoints(0);
        $manager->persist($c8);
        $c9 = new Card();
        $c9->setRank('9');
        $c9->setSuit('c');
        $c9->setPointsTrump(14);
        $c9->setPoints(0);
        $manager->persist($c9);
        $ct = new Card();
        $ct->setRank('t');
        $ct->setSuit('c');
        $ct->setPointsTrump(10);
        $ct->setPoints(10);
        $manager->persist($ct);
        $cj = new Card();
        $cj->setRank('j');
        $cj->setSuit('c');
        $cj->setPointsTrump(20);
        $cj->setPoints(2);
        $manager->persist($cj);
        $cq = new Card();
        $cq->setRank('q');
        $cq->setSuit('c');
        $cq->setPointsTrump(3);
        $cq->setPoints(3);
        $manager->persist($cq);
        $ck = new Card();
        $ck->setRank('k');
        $ck->setSuit('c');
        $ck->setPointsTrump(4);
        $ck->setPoints(4);
        $manager->persist($ck);
        $ca = new Card();
        $ca->setRank('a');
        $ca->setSuit('c');
        $ca->setPointsTrump(11);
        $ca->setPoints(11);
        $manager->persist($ca);    }
}
