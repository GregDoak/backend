<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\AuditConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Controller\Api\ApiController;
use App\Helper\AuditHelper;
use App\Helper\ResponseHelper;
use DataDog\AuditBundle\Entity\AuditLog;
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
     * @Security("is_granted('ROLE_ADMIN')", message=AUDIT_GET_AUDIT_LOGS_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getAuditLogs(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();
        $auditLogRepository = $this->entityManager->getRepository(EntityConstant::AUDIT_LOG);

        $auditLogs = $auditLogRepository->findBy([], [LabelConstant::ID => 'DESC']);
        $simpleAuditLogs = [];
        if (\count($auditLogs) > 0) {
            /** @var AuditLog $auditLog */
            foreach ($auditLogs as $auditLog) {
                $simpleAuditLog = [
                    LabelConstant::ID => $auditLog->getId(),
                    LabelConstant::ACTION => AuditHelper::getAction($auditLog->getAction()),
                    LabelConstant::TABLE => $auditLog->getTbl(),
                    LabelConstant::SOURCE => AuditHelper::getEntity($this->entityManager, $auditLog->getSource()),
                    LabelConstant::TARGET => $auditLog->getTarget() !== null ? AuditHelper::getEntity(
                        $this->entityManager,
                        $auditLog->getTarget()
                    ) : null,
                    LabelConstant::UPDATED_BY => $auditLog->getBlame() !== null ? AuditHelper::getEntity(
                        $this->entityManager,
                        $auditLog->getBlame()
                    ) : null,
                    LabelConstant::CHANGES => \is_array($auditLog->getDiff()) ? \count($auditLog->getDiff()) : 1,
                    LabelConstant::UPDATED_ON => $auditLog->getLoggedAt(),
                ];

                if ($simpleAuditLog[LabelConstant::SOURCE] !== null && $simpleAuditLog[LabelConstant::UPDATED_BY] !== null) {
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
     * @Security("is_granted('ROLE_ADMIN')", message=AUDIT_GET_AUDIT_LOG_SECURITY_ERROR)
     * @ParamConverter("auditLog", class="DataDog\AuditBundle\Entity\AuditLog", options={"id" = "id"})
     * @param AuditLog $auditLog
     * @throws \LogicException
     * @return View
     */
    public function getAuditLog(AuditLog $auditLog): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();

        $simpleAuditLog = [
            LabelConstant::ID => $auditLog->getId(),
            LabelConstant::ACTION => AuditHelper::getAction($auditLog->getAction()),
            LabelConstant::TABLE => $auditLog->getTbl(),
            LabelConstant::SOURCE => AuditHelper::getEntity($this->entityManager, $auditLog->getSource()),
            LabelConstant::TARGET => $auditLog->getTarget() !== null ? AuditHelper::getEntity(
                $this->entityManager,
                $auditLog->getTarget()
            ) : null,
            LabelConstant::UPDATED_BY => $auditLog->getBlame() !== null ? AuditHelper::getEntity(
                $this->entityManager,
                $auditLog->getBlame()
            ) : null,
            LabelConstant::CHANGES => $auditLog->getDiff(),
            LabelConstant::UPDATED_ON => $auditLog->getLoggedAt(),
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
