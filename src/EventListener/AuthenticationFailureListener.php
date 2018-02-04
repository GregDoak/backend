<?php

namespace App\EventListener;

use App\Constant\Security\AuthenticationConstant;
use App\Helper\ResponseHelper;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AuthenticationFailureListener
 * @package App\EventListener
 */
class AuthenticationFailureListener
{
    private const AUTHENTICATION_FAILURE_CODE = 401;

    protected $logger;

    /**
     * AuthenticationFailureListener constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getException();
        $user = $exception->getToken();

        if ($exception instanceof \Exception) {
            $code = $exception->getCode() !== 0 ? $exception->getCode() : self::AUTHENTICATION_FAILURE_CODE;
            $message = $exception->getMessage() !== 'Bad credentials.' ?
                $exception->getMessage() : AuthenticationConstant::INVALID_CREDENTIALS;
        } else {
            $code = self::AUTHENTICATION_FAILURE_CODE;
            $message = AuthenticationConstant::UNKNOWN_ERROR;
        }

        $data = ResponseHelper::buildErrorResponse($code, $message);

        $this->logger->info(
            $this->getClassName(),
            [
                'code' => $code,
                'message' => $message,
                'username' => ($user !== null) ? $user->getUsername() : 'Unknown',
            ]
        );

        $response = new JsonResponse($data, $code);

        $event->setResponse($response);
    }

    /**
     * @throws
     * @return string
     */
    private function getClassName(): string
    {
        $class = new \ReflectionClass($this);

        return $class->getName();
    }
}