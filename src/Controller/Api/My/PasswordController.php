<?php

namespace App\Controller\Api\My;

use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Constant\My\PasswordConstant;
use App\Controller\Api\ApiController;
use App\Helper\ResponseHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PasswordController
 * @package App\Controller\Api\My
 */
class PasswordController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @Rest\Put("/my/password.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=PASSWORD_UPDATE_PASSWORD_SECURITY_ERROR)
     * @param Request $request
     * @throws ORMException
     * @return View
     */
    public function updatePassword(Request $request): View
    {
        $encoder = $this->get('security.password_encoder');
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get(EntityConstant::ENTITY_MANAGER);

        try {

            if ( ! $encoder->isPasswordValid($this->authenticatedUser,
                $request->get(LabelConstant::CURRENT_PASSWORD))) {
                $this->setEntityError(PasswordConstant::UPDATE_PASSWORD_INCORRECT);
            }

            if ($request->get(LabelConstant::PASSWORD) !== $request->get(LabelConstant::CONFIRM_PASSWORD)) {
                $this->setEntityError(PasswordConstant::UPDATE_PASSWORD_NOT_MATCHING);
            }

            if ($request->get(LabelConstant::CURRENT_PASSWORD) === $request->get(LabelConstant::PASSWORD)) {
                $this->setEntityError(PasswordConstant::UPDATE_PASSWORD_MATCHING);
            }

            $this->authenticatedUser->setPlainPassword($request->get(LabelConstant::PASSWORD));

            $this->validateEntity($this->authenticatedUser, PasswordConstant::UPDATE_PASSWORD_VALIDATION_MESSAGE);
            $password = $this->get('security.password_encoder')->encodePassword(
                $this->authenticatedUser,
                $this->authenticatedUser->getPlainPassword()
            );

            $this->authenticatedUser
                ->setPassword($password)
                ->setPasswordCreatedOn()
                ->setExpired(false)
                ->setUpdatedBy($this->authenticatedUser);

            $this->entityManager->persist($this->authenticatedUser);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                AppConstant::SUCCESS_TYPE,
                PasswordConstant::UPDATE_PASSWORD_SUCCESS_MESSAGE
            );

            $data = ResponseHelper::buildSuccessResponse(200, $data);

            ResponseHelper::logResponse(
                PasswordConstant::UPDATE_PASSWORD_SUCCESS_LOG,
                $data,
                $this
            );

        } catch (\UnexpectedValueException $exception) {
            $data = ResponseHelper::buildErrorResponse(
                $exception->getCode(),
                $exception->getMessage(),
                $this->getEntityErrors()
            );

            ResponseHelper::logResponse(PasswordConstant::UPDATE_PASSWORD_ERROR_LOG, $data, $this);
        }

        return $this->view($data, $data['code']);
    }
}