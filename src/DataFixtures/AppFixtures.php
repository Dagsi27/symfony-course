<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $microPost1 = new MicroPost();
        $microPost1->setTitle('Welcome to Poland!');
        $microPost1->setText('Hello World!');
        $microPost1->setCreated(new \DateTime());
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Welcome to US!');
        $microPost2->setText('Hello World!');
        $microPost2->setCreated(new \DateTime());
        $manager->persist($microPost2);

        $microPost23 = new MicroPost();
        $microPost23->setTitle('Welcome to DE!');
        $microPost23->setText('Hello World!');
        $microPost23->setCreated(new \DateTime());
        $manager->persist($microPost23);

        $manager->flush();
    }
}
