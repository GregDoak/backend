<?php

namespace App\Controller\Api\My;

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
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        try {

            if ( ! $this->get('security.password_encoder')->isPasswordValid(
                $this->authenticatedUser,
                $request->get('currentPassword')
            )) {
                $this->setEntityError('Your current password is incorrect.');
            }

            if ($request->get('password') !== $request->get('confirmPassword')) {
                $this->setEntityError('Your new passwords do not match.');
            }

            $this->authenticatedUser->setPlainPassword($request->get('password'));

            $this->validateEntity($this->authenticatedUser, PasswordConstant::UPDATE_PASSWORD_VALIDATION_MESSAGE);
            $password = $this->get('security.password_encoder')->encodePassword(
                $this->authenticatedUser,
                $this->authenticatedUser->getPlainPassword()
            );
            $this->authenticatedUser->setPassword($password);

            $this->entityManager->persist($this->authenticatedUser);
            $this->entityManager->flush();

            $data = ResponseHelper::buildMessageResponse(
                'success',
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