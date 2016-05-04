<?php

/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 04/05/2016
 * Time: 20:57
 */

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Quote;

class LoadContent implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @return mixed
     */
    public function load(ObjectManager $manager)
    {
        /** The user how will write the quotes */
        $user = $manager->getRepository("AppBundle:User")->findOneByIp("127.0.0.1");
        if (!$user) {
            $user = new User();
            $user->setIp("127.0.0.1");
            $user->setArrivalDate(new \DateTime());
            $user->setLastConnexion(new \DateTime());

            $manager->persist($user);
            $manager->flush();
        }

        for ($i = 0; $i < 60; $i++) {
            $quote = new Quote();
            $quote->setAuthor($user);
            $quote->setContent("Content ".$i);
            $quote->setDate(new \DateTime());
            $manager->persist($quote);
        }
        $manager->flush();
    }
}