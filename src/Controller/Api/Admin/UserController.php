<?php

namespace App\Controller\Api\Admin;

use App\Constant\Admin\UserConstant;
use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Controller\Api\ApiController;
use App\Entity\Security\User;
use App\Exception\ValidationException;
use App\Helper\ResponseHelper;
use App\Helper\UserHelper;
use App\Repository\Security\UserRepository;
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
 * Class UserController
 * @package App\Controller\Api\Admin
 */
class UserController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;
    /** @var UserRepository $userRepository */
    private $userRepository;

    /**
     * @Rest\Get("/admin/users.{_format}", defaults={"_format"="json"})
     * @Security("is_granted('ROLE_ADMIN')", message=USER_GET_USERS_SECURITY_ERROR)
     * @throws \LogicException
     * @return View
     */
    public function getUsers(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        $users = $this->userRepository->getUsers();

        $data = ResponseHelper::buildSuccessResponse(200, $users);

        ResponseHelper::logResponse(UserConstant::GET_MULTIPLE_SUCCESS_MESSAGE, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Get("/admin/user/{id}.{_format}", defaults={"_format"="json"})
     * @Security("is_granted('ROLE_ADMIN')",message=USER_GET_USER_SECURITY_ERROR)
     * @ParamConverter("user", class="App\Entity\Security\User", options={"id" = "id"})
     * @param User $user
     * @throws \LogicException
     * @return View
     */
    public function getSingleUser(User $user): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();
        $this->userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        $data = ResponseHelper::buildSuccessResponse(200, $user);

        ResponseHelper::logResponse(
            sprintf(UserConstant::GET_SINGLE_SUCCESS_MESSAGE, $user->getUsername()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Post("/admin/user.{_format}", defaults={"_format"="json"})
     * @Security("is_granted('ROLE_ADMIN')", message=USER_CREATE_USER_SECURITY_ERROR)
     * @param Request $request
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function createUser(Request $request): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();
        $encoder = $this->get('security.password_encoder');

        try {
            $user = new User();
            $user
                ->setUsername($request->get(LabelConstant::USERNAME))
                ->setPlainPassword($request->get(LabelConstant::PASSWORD))
                ->setLoginCount()
                ->setEnabled(true)
                ->setExpired(true)
                ->setCreatedBy($this->authenticatedUser);

            UserHelper::setGroups($user, (array)$request->get(LabelConstant::GROUPS), $this->entityManager);
            UserHelper::setRoles($user, (array)$request->get(LabelConstant::ROLES), $this->entityManager);

            $this->validateEntity($user, UserConstant::CREATE_VALIDATION_ERROR);
            $user->setPassword($encoder->encodePassword($user, $user->getPlainPassword()));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                AppConstant::SUCCESS_TYPE,
                sprintf(UserConstant::CREATE_SUCCESS_MESSAGE, $user->getUsername())
            );

            $data = ResponseHelper::buildSuccessResponse(201, $data);

            ResponseHelper::logResponse(
                sprintf(UserConstant::CREATE_SUCCESS_LOG, $user->getUsername()),
                $data,
                $this
            );

        } catch (ValidationException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(UserConstant::CREATE_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Put("/admin/user/{id}.{_format}", defaults={"_format"="json"})
     * @Security("is_granted('ROLE_ADMIN')", message=USER_UPDATE_USER_SECURITY_ERROR)
     * @ParamConverter("user", class="App\Entity\Security\User", options={"id" = "id"})
     * @param Request $request
     * @param User $user
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function updateUser(Request $request, User $user): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();
        $sourceUser = clone $user;

        try {
            $user
                ->setUsername(trim($request->get(LabelConstant::USERNAME)))
                ->setPlainPassword($user->getPassword())
                ->setUpdatedBy($this->authenticatedUser)
                ->setUpdatedOn()
                ->clearGroups()
                ->clearRoles();

            UserHelper::setGroups($user, (array)$request->get(LabelConstant::GROUPS), $this->entityManager);
            UserHelper::setRoles($user, (array)$request->get(LabelConstant::ROLES), $this->entityManager);

            $this->validateEntity($user, UserConstant::UPDATE_VALIDATION_ERROR);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                AppConstant::SUCCESS_TYPE,
                sprintf(UserConstant::UPDATE_SUCCESS_MESSAGE, $sourceUser->getUsername())
            );

            $data = ResponseHelper::buildSuccessResponse(200, $data);

            ResponseHelper::logResponse(
                sprintf(UserConstant::UPDATE_SUCCESS_LOG, $sourceUser->getUsername()),
                $data,
                $this
            );

        } catch (ValidationException $exception) {

            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(
                sprintf(UserConstant::UPDATE_ERROR_LOG, $sourceUser->getUsername()),
                $data,
                $this
            );
        }

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Delete("/admin/user/{id}.{_format}", defaults={"_format"="json"})
     * @Security("is_granted('ROLE_ADMIN')", message=USER_DELETE_USER_SECURITY_ERROR)
     * @ParamConverter("user", class="App\Entity\Security\User", options={"id" = "id"})
     * @param User $user
     * @throws \LogicException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @return View
     */
    public function deleteUser(User $user): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->getDoctrine()->getManager();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $data = ResponseHelper::buildMessageResponse(
            AppConstant::SUCCESS_TYPE,
            sprintf(UserConstant::DELETE_SUCCESS_MESSAGE, $user->getUsername())
        );

        $data = ResponseHelper::buildSuccessResponse(200, $data);

        ResponseHelper::logResponse(
            sprintf(UserConstant::DELETE_SUCCESS_LOG, $user->getUsername()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }
}
