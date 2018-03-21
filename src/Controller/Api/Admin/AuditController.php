<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\AuditConstant;
use App\Controller\Api\ApiController;
use App\Helper\AuditHelper;
use App\Helper\ResponseHelper;
use DataDog\AuditBundle\Entity\Association;
use DataDog\AuditBundle\Entity\AuditLog;
use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     * @Security("has_role('ROLE_ADMIN')", message=AUDIT_GET_AUDIT_LOGS_SECURITY_ERROR)
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
                    'action' => AuditHelper::getAction($auditLog->getAction()),
                    'table' => $auditLog->getTbl(),
                    'source' => AuditHelper::getEntity($this->entityManager, $auditLog->getSource()),
                    'target' => $auditLog->getTarget() !== null ? AuditHelper::getEntity(
                        $this->entityManager,
                        $auditLog->getTarget()
                    ) : null,
                    'updatedBy' => $auditLog->getBlame() !== null ? AuditHelper::getEntity(
                        $this->entityManager,
                        $auditLog->getBlame()
                    ) : null,
                    'changes' => count($auditLog->getDiff()),
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

    /**
     * @Rest\Get("/admin/audit-log/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=AUDIT_GET_AUDIT_LOG_SECURITY_ERROR)
     * @ParamConverter("auditLog", class="DataDog\AuditBundle\Entity\AuditLog", options={"id" = "id"})
     * @param AuditLog $auditLog
     * @throws \LogicException
     * @return View
     */
    public function getAuditLog(AuditLog $auditLog)
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        $simpleAuditLog = [
            'id' => $auditLog->getId(),
            'action' => AuditHelper::getAction($auditLog->getAction()),
            'table' => $auditLog->getTbl(),
            'source' => AuditHelper::getEntity($this->entityManager, $auditLog->getSource()),
            'target' => $auditLog->getTarget() !== null ? AuditHelper::getEntity(
                $this->entityManager,
                $auditLog->getTarget()
            ) : null,
            'updatedBy' => $auditLog->getBlame() !== null ? AuditHelper::getEntity(
                $this->entityManager,
                $auditLog->getBlame()
            ) : null,
            'changes' => $auditLog->getDiff(),
            'updatedOn' => $auditLog->getLoggedAt(),
        ];

        $data = ResponseHelper::buildSuccessResponse(200, $simpleAuditLog);

        ResponseHelper::logResponse(
            sprintf(AuditConstant::GET_SINGLE_SUCCESS_MESSAGE, $auditLog->getId()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);

    }

}