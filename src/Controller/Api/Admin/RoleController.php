<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\RoleConstant;
use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Controller\Api\ApiController;
use App\Entity\Security\Role;
use App\Helper\ResponseHelper;
use App\Repository\Security\RoleRepository;
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
 * Class RoleController
 * @package App\Controller\Api\Admin
 */
class RoleController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;
    /** @var RoleRepository $roleRepository */
    private $roleRepository;

    /**
     * @Rest\Get("/admin/roles.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=ROLE_GET_ROLES_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getRoles(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);
        $this->roleRepository = $this->entityManager->getRepository(EntityConstant::ROLE);

        $roles = $this->roleRepository->getRoles();

        $data = ResponseHelper::buildSuccessResponse(200, $roles);

        ResponseHelper::logResponse(RoleConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Get("/admin/role/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')",message=ROLE_GET_ROLE_SECURITY_ERROR)
     * @ParamConverter("role", class="App\Entity\Security\Role", options={"id" = "id"})
     * @param Role $role
     * @throws \LogicException
     * @return View
     */
    public function getRole(Role $role): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);
        $this->roleRepository = $this->entityManager->getRepository(EntityConstant::ROLE);

        $data = ResponseHelper::buildSuccessResponse(200, $role);

        ResponseHelper::logResponse(sprintf(RoleConstant::GET_SINGLE_SUCCESS_MESSAGE, $role->getTitle()), $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Post("/admin/role.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=ROLE_CREATE_ROLE_SECURITY_ERROR)
     * @param Request $request
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function createRole(Request $request): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);

        try {
            $role = new Role();
            $role
                ->setTitle(strtoupper($request->get(LabelConstant::TITLE)))
                ->setDescription($request->get(LabelConstant::DESCRIPTION))
                ->setCreatedBy($this->authenticatedUser);

            $this->validateEntity($role, RoleConstant::CREATE_VALIDATION_ERROR);

            $this->entityManager->persist($role);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                AppConstant::SUCCESS_TYPE,
                sprintf(RoleConstant::CREATE_SUCCESS_MESSAGE, $role->getTitle())
            );

            $data = ResponseHelper::buildSuccessResponse(201, $data);

            ResponseHelper::logResponse(
                sprintf(RoleConstant::CREATE_SUCCESS_LOG, $role->getTitle()),
                $data,
                $this
            );

        } catch (\UnexpectedValueException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(RoleConstant::CREATE_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Put("/admin/role/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=ROLE_UPDATE_ROLE_SECURITY_ERROR)
     * @ParamConverter("role", class="App\Entity\Security\Role", options={"id" = "id"})
     * @param Request $request
     * @param Role $role
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function updateRole(Request $request, Role $role): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);
        $sourceRole = clone $role;

        try {
            $role
                ->setTitle($request->get(LabelConstant::TITLE))
                ->setDescription($request->get(LabelConstant::DESCRIPTION))
                ->setUpdatedBy($this->authenticatedUser)
                ->setUpdatedOn();

            $this->validateEntity($role, RoleConstant::UPDATE_VALIDATION_ERROR);

            $this->entityManager->persist($role);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                AppConstant::SUCCESS_TYPE,
                sprintf(RoleConstant::UPDATE_SUCCESS_MESSAGE, $sourceRole->getTitle())
            );

            $data = ResponseHelper::buildSuccessResponse(200, $data);

            ResponseHelper::logResponse(
                sprintf(RoleConstant::UPDATE_SUCCESS_LOG, $sourceRole->getTitle()),
                $data,
                $this
            );

        } catch (\UnexpectedValueException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(sprintf(RoleConstant::UPDATE_ERROR_LOG, $sourceRole->getTitle()), $data, $this);
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Delete("/admin/role/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_ADMIN')", message=ROLE_DELETE_ROLE_SECURITY_ERROR)
     * @ParamConverter("role", class="App\Entity\Security\Role", options={"id" = "id"})
     * @param Role $role
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function deleteRole(Role $role): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);

        $this->entityManager->remove($role);
        $this->entityManager->flush();

        $data = ResponseHelper::buildMessageResponse(
            AppConstant::SUCCESS_TYPE,
            sprintf(RoleConstant::DELETE_SUCCESS_MESSAGE, $role->getTitle())
        );

        $data = ResponseHelper::buildSuccessResponse(200, $data);

        ResponseHelper::logResponse(
            sprintf(RoleConstant::DELETE_SUCCESS_LOG, $role->getTitle()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }
}