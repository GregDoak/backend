<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\GroupConstant;
use App\Controller\Api\ApiController;
use App\Entity\Security\Group;
use App\Helper\GroupHelper;
use App\Helper\ResponseHelper;
use App\Repository\Security\GroupRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GroupController
 * @package App\Controller\Api\Admin
 */
class GroupController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;
    /** @var GroupRepository $groupRepository */
    private $groupRepository;

    /**
     * @Rest\Get("/admin/groups.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=GROUP_GET_GROUPS_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getGroups(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $this->groupRepository = $this->entityManager->getRepository('App:Security\Group');

        $groups = $this->groupRepository->getGroups();

        $data = ResponseHelper::buildSuccessResponse(200, $groups);

        ResponseHelper::logResponse(GroupConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Get("/admin/group/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')",message=GROUP_GET_GROUP_SECURITY_ERROR)
     * @ParamConverter("group", class="App\Entity\Security\Group", options={"id" = "id"})
     * @param Group $group
     * @throws \LogicException
     * @return View
     */
    public function getGroup(Group $group): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $this->groupRepository = $this->entityManager->getRepository('App:Security\Group');

        $data = ResponseHelper::buildSuccessResponse(200, $group);

        ResponseHelper::logResponse(
            sprintf(GroupConstant::GET_SINGLE_SUCCESS_MESSAGE, $group->getTitle()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Post("/admin/group.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=GROUP_CREATE_GROUP_SECURITY_ERROR)
     * @param Request $request
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function createGroup(Request $request): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        try {
            $group = new Group();
            $group
                ->setTitle($request->get('title'))
                ->setDescription($request->get('description'))
                ->setCreatedBy($this->authenticatedUser);

            GroupHelper::setRoles($group, (array)$request->get('roles'), $this->entityManager);

            $this->validateEntity($group, GroupConstant::CREATE_VALIDATION_ERROR);

            $this->entityManager->persist($group);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                'success',
                sprintf(GroupConstant::CREATE_SUCCESS_MESSAGE, $group->getTitle())
            );

            $data = ResponseHelper::buildSuccessResponse(201, $data);

            ResponseHelper::logResponse(
                sprintf(GroupConstant::CREATE_SUCCESS_LOG, $group->getTitle()),
                $data,
                $this
            );

        } catch (\UnexpectedValueException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(GroupConstant::CREATE_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Put("/admin/group/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=GROUP_UPDATE_GROUP_SECURITY_ERROR)
     * @ParamConverter("group", class="App\Entity\Security\Group", options={"id" = "id"})
     * @param Request $request
     * @param Group $group
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function updateGroup(Request $request, Group $group): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $sourceGroup = clone $group;

        try {
            $group
                ->setTitle(strtoupper($request->get('title')))
                ->setDescription($request->get('description'))
                ->setUpdatedBy($this->authenticatedUser)
                ->setUpdatedOn();

            GroupHelper::setRoles($group, (array)$request->get('roles'), $this->entityManager);

            $this->validateEntity($group, GroupConstant::UPDATE_VALIDATION_ERROR);

            $this->entityManager->persist($group);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                'success',
                sprintf(GroupConstant::UPDATE_SUCCESS_MESSAGE, $sourceGroup->getTitle())
            );

            $data = ResponseHelper::buildSuccessResponse(200, $data);

            ResponseHelper::logResponse(
                sprintf(GroupConstant::UPDATE_SUCCESS_LOG, $sourceGroup->getTitle()),
                $data,
                $this
            );

        } catch (\UnexpectedValueException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(
                sprintf(GroupConstant::UPDATE_ERROR_LOG, $sourceGroup->getTitle()),
                $data,
                $this
            );
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Delete("/admin/group/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=GROUP_DELETE_GROUP_SECURITY_ERROR)
     * @ParamConverter("group", class="App\Entity\Security\Group", options={"id" = "id"})
     * @param Group $group
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function deleteGroup(Group $group): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        $this->entityManager->remove($group);
        $this->entityManager->flush();

        $data = ResponseHelper::buildMessageResponse(
            'success',
            sprintf(GroupConstant::DELETE_SUCCESS_MESSAGE, $group->getTitle())
        );

        $data = ResponseHelper::buildSuccessResponse(200, $data);

        ResponseHelper::logResponse(
            sprintf(GroupConstant::DELETE_SUCCESS_LOG, $group->getTitle()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }
}