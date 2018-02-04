<?php

namespace App\Helper;

use DataDog\AuditBundle\Entity\Association;
use Doctrine\ORM\EntityManager;

/**
 * Class AuditHelper
 * @package App\Helper
 */
class AuditHelper
{
    /**
     * @param EntityManager $entityManager
     * @param Association $association
     * @return array
     */
    public static function getEntity(EntityManager $entityManager, Association $association)
    {
        $entityRepository = $entityManager->getRepository($association->getClass());
        $entity = $entityRepository->find($association->getFk());
        if ($entity === null) {
            $entity = [
                'title' => $association->getLabel(),
            ];
        }

        return [
            'interface' => self::convertClassToInterface($association->getClass()),
            'class' => $association->getClass(),
            'entity' => $entity,
        ];
    }

    /**
     * @param string $class
     * @return string
     */
    private static function convertClassToInterface(string $class): string
    {
        $namespaces = explode('\\', $class);

        return $namespaces[\count($namespaces) - 1];
    }
}