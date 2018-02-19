<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\AuditConstant;
use App\Controller\Api\ApiController;
use App\Helper\AuditHelper;
use App\Helper\ResponseHelper;
use DataDog\AuditBundle\Entity\Association;
use DataDog\AuditBundle\Entity\AuditLog;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AuditController
 * @package App\Controller\Api\Admin
 */
class AuditController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @Rest\Get("/admin/audit-logs.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=AUDIT_GET_AUDIT_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getAuditLogs()
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $auditLogRepository = $this->entityManager->getRepository('DataDogAuditBundle:AuditLog');

        $auditLogs = $auditLogRepository->findBy([], ['id' => 'DESC']);
        $simpleAuditLogs = [];
        if (\count($auditLogs) > 0) {
            /** @var AuditLog $auditLog */
            foreach ($auditLogs as $auditLog) {
                $simpleAuditLog = [
                    'id' => $auditLog->getId(),
                    'action' => $auditLog->getAction(),
                    'table' => $auditLog->getTbl(),
                    'source' => AuditHelper::getEntity($this->entityManager, $auditLog->getSource()),
                    'updatedBy' => AuditHelper::getEntity($this->entityManager, $auditLog->getBlame()),
                    'changes' => $auditLog->getDiff(),
                    'updatedOn' => $auditLog->getLoggedAt(),
                ];

                if ($simpleAuditLog['source'] !== null && $simpleAuditLog['updatedBy'] !== null) {
                    $simpleAuditLogs[] = $simpleAuditLog;
                }
            }
        }

        $data = ResponseHelper::buildSuccessResponse(200, $simpleAuditLogs);

        ResponseHelper::logResponse(AuditConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);

    }

}