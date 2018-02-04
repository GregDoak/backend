<?php

namespace App\EventListener;

use App\Helper\ResponseHelper;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Monolog\Logger;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AuthenticationSuccessListener
 * @package App\EventListener
 */
class AuthenticationSuccessListener
{
    protected $logger;

    /**
     * AuthenticationSuccessListener constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $payload = $event->getData();
        $user = $event->getUser();

        if ( ! $user instanceof UserInterface) {
            return;
        }

        $data = ResponseHelper::buildSuccessResponse(201, ['token' => $payload['token']]);

        $this->logger->info(
            $this->getClassName(),
            [
                'code' => 201,
                'message' => sprintf('User %s successfully logged in', $user->getUsername()),
                'username' => $user->getUsername(),
            ]
        );

        $event->setData($data);
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