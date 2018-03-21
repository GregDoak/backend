<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\CronConstant;
use App\Controller\Api\ApiController;
use App\Helper\ResponseHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use GregDoak\CronBundle\Entity\CronJobTask;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class CronController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @Rest\Get("/admin/cron-jobs.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=CRON_GET_CRON_JOBS_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getCronJobs(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $cronJobRepository = $this->entityManager->getRepository('GregDoakCronBundle:CronJob');

        // $cronJobs = $cronJobRepository->getCronJobHistory();
        $cronJobs = $cronJobRepository->findAll();

        $data = ResponseHelper::buildSuccessResponse(200, $cronJobs);

        ResponseHelper::logResponse(CronConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Get("/admin/cron-job-tasks.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=CRON_GET_CRON_JOB_TASKS_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getCronJobTasks(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $cronJobTaskRepository = $this->entityManager->getRepository('GregDoakCronBundle:CronJobTask');

        $cronJobTasks = $cronJobTaskRepository->getActiveTasks();

        $data = ResponseHelper::buildSuccessResponse(200, $cronJobTasks);

        ResponseHelper::logResponse(CronConstant::GET_MULTIPLE_TASKS_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Get("/admin/cron-job-task/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')",message=CRON_GET_CRON_JOB_TASK_SECURITY_ERROR)
     * @ParamConverter("cronJobTask", class="GregDoak\CronBundle\Entity\CronJobTask", options={"id" = "id"})
     * @param CronJobTask $cronJobTask
     * @throws \LogicException
     * @return View
     */
    public function getCronJobTask(CronJobTask $cronJobTask): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        $data = ResponseHelper::buildSuccessResponse(200, $cronJobTask);

        ResponseHelper::logResponse(
            sprintf(CronConstant::GET_SINGLE_TASK_SUCCESS_MESSAGE, $cronJobTask->getCommand()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Post("/admin/cron-job-task.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=CRON_CREATE_CRON_JOB_TASK_SECURITY_ERROR)
     * @param Request $request
     * @throws \LogicException
     * @throws ORMException
     * @throws OptimisticLockException
     * @return View
     */
    public function createCronJobTask(Request $request): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        try {
            $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $request->get('startDate'));
            $intervalPeriod = (int)$request->get('intervalPeriod');
            $intervalContext = $request->get('intervalContext');
            $priority = (int)$request->get('priority');

            if ($startDate === false || $startDate->format('Y-m-d H:i:s') !== $request->get('startDate')) {
                throw new \UnexpectedValueException(CronConstant::START_DATE_VALIDATION, 400);
            }

            if ($intervalPeriod < 1) {
                throw new \UnexpectedValueException(CronConstant::INTERVAL_PERIOD_VALIDATION, 400);
            }

            if ( ! in_array($intervalContext, CronConstant::INTERVAL_CONTEXT_OPTIONS)) {
                throw new \UnexpectedValueException(
                    sprintf(
                        CronConstant::INTERVAL_CONTEXT_VALIDATION,
                        implode(', ', CronConstant::INTERVAL_CONTEXT_OPTIONS)
                    ), 400
                );
            }

            if ($priority < 1 || $priority > 10) {
                throw new \UnexpectedValueException(CronConstant::PRIORITY_VALIDATION, 400);
            }

            $cronJobTask = new CronJobTask();
            $cronJobTask
                ->setCommand($request->get('command'))
                ->setStartDate($startDate)
                ->setIntervalPeriod($intervalPeriod)
                ->setIntervalContext($intervalContext)
                ->setPriority($priority)
                ->setNextRun();

            $this->validateEntity($cronJobTask, CronConstant::CREATE_VALIDATION_ERROR);

            $this->entityManager->persist($cronJobTask);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                'success',
                sprintf(CronConstant::CREATE_SUCCESS_MESSAGE, $cronJobTask->getCommand())
            );

            $data = ResponseHelper::buildSuccessResponse(201, $data);

            ResponseHelper::logResponse(
                sprintf(CronConstant::CREATE_SUCCESS_LOG, $cronJobTask->getCommand()),
                $data,
                $this
            );

        } catch (\UnexpectedValueException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(CronConstant::CREATE_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }
}