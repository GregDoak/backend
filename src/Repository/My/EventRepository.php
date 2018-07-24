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
     * @return Event[]
     */
    public function getEvents(User $user): array
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.createdBy = :createdUserId')
            ->andWhere('e.endDateTime >= :now')
            ->andWhere('e.active = :active')
            ->setParameter('createdUserId', $user->getId())
            ->setParameter('now', new \DateTime())
            ->setParameter('active', true)
            ->orderBy('e.startDateTime', 'ASC')
            ->addOrderBy('e.endDateTime', 'ASC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUpcomingEvents(User $user): array
    {
        $query = $this->createQueryBuilder('e')
            ->leftJoin('e.users', 'u')
            ->where('e.createdBy = :createdUserId')
            ->orWhere('u.id = :participantUserId')
            ->andWhere('e.endDateTime >= :now')
            ->andWhere('e.active = :active')
            ->setParameter('createdUserId', $user->getId())
            ->setParameter('participantUserId', $user->getId())
            ->setParameter('now', new \DateTime())
            ->setParameter('active', true)
            ->orderBy('e.startDateTime', 'ASC')
            ->addOrderBy('e.endDateTime', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
