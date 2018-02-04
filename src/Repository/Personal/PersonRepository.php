<?php

namespace App\Repository\Personal;

use App\Entity\Personal\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PersonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Person::class);
    }

    /**
     * @return array
     */
    public function getPersons(): array
    {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
