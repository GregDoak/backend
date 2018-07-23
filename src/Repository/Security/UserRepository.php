<?php

namespace App\Repository\Security;

use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 * @package App\Repository\Security
 */
class UserRepository extends ServiceEntityRepository
{
    private const USERNAME_FIELD = 'u.username';

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
            ->orderBy(self::USERNAME_FIELD, 'ASC')
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

    /**
     * @param User $user
     * @return mixed
     */
    public function getOtherUsers(User $user)
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.id != :userId')
            ->setParameter('userId', $user->getId())
            ->orderBy(self::USERNAME_FIELD, 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUsersBySameRole(User $user): array
    {
        $query = $this->createQueryBuilder('u')
            ->innerJoin('u.roles', 'r')
            ->where('r.title IN (:roles)')
            ->andWhere('r.title != :roleTitle')
            ->andWhere('u.id != :userId')
            ->setParameter('roles', $user->getRoles(), Connection::PARAM_STR_ARRAY)
            ->setParameter('roleTitle', 'ROLE_USER')
            ->setParameter('userId', $user->getId())
            ->orderBy(self::USERNAME_FIELD, 'ASC')
            ->getQuery();

        return $query->getResult();
    }

}
