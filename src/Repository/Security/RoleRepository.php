<?php

namespace App\Repository\Security;

use App\Entity\Security\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RoleRepository
 * @package App\Repository\Security
 */
class RoleRepository extends ServiceEntityRepository
{
    /**
     * RoleRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Role::class);
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        $query = $this->createQueryBuilder('r')
            ->innerJoin('r.createdBy', 'u')
            ->orderBy('r.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $title
     * @return Role|null
     */
    public function getRoleByTitle(string $title): ?Role
    {
        $role = $this->findOneBy(['title' => strtoupper($title)]);

        return $role instanceof Role ? $role : null;
    }
}
