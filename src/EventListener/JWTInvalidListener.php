<?php

namespace App\EventListener;

use App\Constant\Security\AuthenticationConstant;
use App\Helper\ResponseHelper;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JWTInvalidListener
 * @package App\EventListener
 */
class JWTInvalidListener
{

    protected $logger;

    /**
     * JWTInvalidListener constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event): void
    {
        $data = $this->handleError(AuthenticationConstant::JWT_EXPIRED);

        $response = new JsonResponse($data, 403);

        $event->setResponse($response);
    }

    /**
     * @param string $message
     * @return array
     */
    private function handleError(string $message): array
    {
        $data = ResponseHelper::buildErrorResponse(401, $message);

        $this->logger->info(
            $this->getClassName(),
            [
                'code' => $data['code'],
                'message' => $data['data']['message'],
                'username' => 'Unknown',
            ]
        );

        return $data;
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

    /**
     * @param JWTInvalidEvent $event
     */
    public function onJWTInvalid(JWTInvalidEvent $event): void
    {
        $data = $this->handleError(AuthenticationConstant::JWT_INVALID);

        $response = new JsonResponse($data, 403);

        $event->setResponse($response);
    }

    /**
     * @param JWTNotFoundEvent $event
     */
    public function onJWTNotFound(JWTNotFoundEvent $event): void
    {
        $data = $this->handleError(AuthenticationConstant::JWT_NOT_FOUND);

        $response = new JsonResponse($data, 403);

        $event->setResponse($response);
    }

}