<?php

namespace App\Repository\My;

use App\Entity\My\Event;
use App\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class EventRepository
 * @package App\Repository\Security
 */
class EventRepository extends ServiceEntityRepository
{
    /**
     * EventRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getEvents(User $user): array
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.createdBy = :createdUserId')
            ->orWhere('e.createdBy = :participantUserId')
            ->andWhere('e.endDateTime >= :now')
            ->setParameter('createdUserId', $user->getId())
            ->setParameter('participantUserId', $user->getId())
            ->setParameter('now', new \DateTime())
            ->orderBy('e.endDateTime', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
