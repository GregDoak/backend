<?php

namespace App\Repository\Lookup;

use App\Entity\Lookup\Gender;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GenderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Gender::class);
    }

    /**
     * @return array
     */
    public function getGenders(): array
    {
        $query = $this->createQueryBuilder('g')
            ->orderBy('g.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
