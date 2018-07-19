<?php

namespace App\Helper;

use App\Constant\EntityConstant;
use App\Entity\My\Event;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManager;

class EventHelper
{
    /**
     * @param Event $event
     * @param array $userIds
     * @param EntityManager $entityManager
     */
    public static function setUsers(Event $event, array $userIds, EntityManager $entityManager): void
    {
        foreach ($userIds as $userId) {
            $user = $entityManager->getRepository(EntityConstant::USER)->find($userId);
            if ($user instanceof User) {
                $event->setUser($user);
            }
        }
    }
}