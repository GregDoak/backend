<?php

namespace App\Repository\Security;

use App\Entity\Security\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * @return array
     */
    public function getGroups(): array
    {
        $query = $this->createQueryBuilder('g')
            ->orderBy('g.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
