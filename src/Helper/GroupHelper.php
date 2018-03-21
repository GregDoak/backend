<?php

namespace App\Helper;

use App\Entity\Security\Group;
use App\Entity\Security\Role;
use Doctrine\Common\Util\Debug;
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
        $roles = $group->getRolesCollection()->getValues();
        $source = array_map(
            function (Role $role) {
                return $role->getId();
            },
            $roles
        );
        $add = array_diff($roleIds, $source);
        $remove = array_diff($source, $roleIds);

        $actions = array_merge($add, $remove);

        foreach ($actions as $action) {
            $role = $entityManager->getRepository('App:Security\Role')->find($action);
            if ($role instanceof Role) {
                if (in_array($action, $add)) {
                    $group->setRole($role);
                }
                if (in_array($action, $remove)) {
                    $group->removeRole($role);
                }
            }
        }
    }
}