<?php

namespace App\Repository\Security;

use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 * @package App\Repository\Security
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        $query = $this->createQueryBuilder('u')
            ->orderBy('u.username', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername(string $username): ?User
    {
        $user = $this->findOneBy(['username' => strtoupper($username)]);

        return $user instanceof User ? $user : null;
    }

}
