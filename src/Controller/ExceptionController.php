<?php

namespace App\Controller;

use App\Constant\AppConstant;
use App\Entity\Security\User;
use App\Helper\ResponseHelper;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ExceptionController
 * @package App\Controller
 */
class ExceptionController extends FOSRestController
{
    private $code;
    private $logger;
    private $message;
    private $messages;
    private $username;

    /**
     * ExceptionController constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Exception $exception
     * @throws \LogicException
     * @return View
     */
    public function exception(\Exception $exception): View
    {
        $this->setResponseData($exception);

        $data = ResponseHelper::buildErrorResponse($this->code, $this->message, $this->messages);

        return $this->view($data, $this->code);
    }

    /**
     * @param \Exception $exception
     * @throws \LogicException
     */
    private function setResponseData(\Exception $exception): void
    {
        $authenticatedUser = $this->getUser();
        $this->username = $authenticatedUser instanceof User ? $authenticatedUser->getUsername() : 'Unknown';

        switch ($exception) {
            case $exception instanceof AccessDeniedHttpException:
                $code = Response::HTTP_FORBIDDEN;
                $message = $exception->getMessage();
                $messages = [];
                break;
            case $exception instanceof NotFoundHttpException:
                $code = Response::HTTP_NOT_FOUND;
                $message = AppConstant::HTTP_NOT_FOUND;
                $messages = [];
                break;
            case $exception instanceof MethodNotAllowedHttpException:
                $code = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = AppConstant::HTTP_METHOD_NOT_ALLOWED;
                $messages = [];
                break;
            case $exception instanceof ORMException:
                $code = 500;
                $message = AppConstant::ORM_EXCEPTION;
                $messages = [];
                break;
            default:
                $code = 500;
                $message = AppConstant::DEFAULT_EXCEPTION;
                $messages = [];
        }

        $this->logger->error(
            \get_class($exception),
            [
                'code' => $code,
                'message' => $message,
                'username' => $this->username,
            ]
        );

        $this->code = $code;
        $this->message = $message;
        $this->messages = $messages;
    }
}