<?php

namespace App\Helper;

use App\Entity\Security\Group;
use App\Entity\Security\Role;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManager;

/**
 * Class UserHelper
 * @package App\Helper
 */
class UserHelper
{
    /**
     * @param User $user
     * @param array $groupIds
     * @param EntityManager $entityManager
     */
    public static function setGroups(User &$user, array $groupIds, EntityManager $entityManager): void
    {
        $roleIds = [];
        foreach ($groupIds as $groupId) {
            $group = $entityManager->getRepository('App:Security\Group')->find($groupId);
            if ($group instanceof Group) {
                $roles = $group->getRolesCollection();
                foreach ($roles as $role) {
                    if ($role instanceof Role) {
                        $roleIds[] = $role->getId();
                    }
                }
            }
        }
        $roleIds = array_unique($roleIds);
        self::setRoles($user, $roleIds, $entityManager);
    }

    /**
     * @param User $user
     * @param array $roleIds
     * @param EntityManager $entityManager
     */
    public static function setRoles(User &$user, array $roleIds, EntityManager $entityManager): void
    {
        foreach ($roleIds as $roleId) {
            $role = $entityManager->getRepository('App:Security\Role')->find($roleId);
            if ($role instanceof Role) {
                $user->setRole($role);
            }
        }
    }
}