<?php

/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 05/05/2016
 * Time: 17:52
 */

namespace AppBundle\Utils;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\QuoteRepository;
use AppBundle\Repository\VoteRepository;

class ManagerService
{
    /** @var EntityManager $em */
    private $em;

    /**
     * ManagerService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->setEm($entityManager);
    }

    /**
     * @return UserRepository
     */
    public function getUserRepo()
    {
        return $this->getManager()->getRepository("AppBundle:User");
    }

    /**
     * @return EntityManager
     */
    public function getManager()
    {
        return $this->getEm();
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }

    /**
     * @return QuoteRepository
     */
    public function getQuoteRepo()
    {
        return $this->getManager()->getRepository("AppBundle:Quote");
    }

    /**
     * @return VoteRepository
     */
    public function getVoteRepo()
    {
        return $this->getManager()->getRepository("AppBundle:Vote");
    }

    /**
     * @param object $object
     */
    public function persist($object)
    {
        $this->getManager()->persist($object);
    }

    /**
     * @param null|object|array $entity
     */
    public function flush($entity = null)
    {
        $this->getManager()->flush($entity);
    }

    /**
     * @param $ip
     * @param null $username
     * @return User|null
     */
    public function createUser($ip, $username = null)
    {
        $user = new User();
        if ($username) {
            $user->setUsername($username);
        }
        $user->setArrivalDate(new \DateTime());
        $user->setLastConnexion(new \DateTime());
        $user->setIp($ip);
        //Persist and flush the user
        $this->persist($user);
        $this->flush();

        $userRepo = $this->getUserRepo();

        return isset($username) ? $userRepo->findOneByUsername($username) : $userRepo->findOneByIp($ip);
    }

}