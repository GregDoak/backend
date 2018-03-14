<?php

namespace App\Helper;

use App\Entity\Security\Group;
use App\Entity\Security\Role;
use Doctrine\ORM\EntityManager;

/**
 * Class GroupHelper
 * @package App\Helper
 */
class GroupHelper
{
    /**
     * @param Group $group
     * @param array $roleIds
     * @param EntityManager $entityManager
     */
    public static function setRoles(Group &$group, array $roleIds, EntityManager $entityManager): void
    {
        foreach ($roleIds as $roleId) {
            $role = $entityManager->getRepository('App:Security\Role')->find($roleId);
            if ($role instanceof Role) {
                $group->setRole($role);
            }
        }
    }
}