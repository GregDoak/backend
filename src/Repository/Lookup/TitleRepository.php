<?php

namespace App\Repository\Lookup;

use App\Entity\Lookup\Title;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TitleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Title::class);
    }

    /**
     * @return array
     */
    public function getTitles(): array
    {
        $query = $this->createQueryBuilder('t')
            ->orderBy('t.title', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
