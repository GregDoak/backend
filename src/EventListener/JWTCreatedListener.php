<?php

namespace App\EventListener;

use App\Entity\Security\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Class JWTCreatedListener
 * @package App\EventListener
 */
class JWTCreatedListener
{
    private $passwordIntervalDays;
    private $requestStack;

    /**
     * JWTCreatedListener constructor.
     * @param RequestStack $requestStack
     * @param int $passwordIntervalDays
     */
    public function __construct(RequestStack $requestStack, int $passwordIntervalDays = 0)
    {
        $this->passwordIntervalDays = $passwordIntervalDays;
        $this->requestStack = $requestStack;
    }

    /**
     * @param JWTCreatedEvent $event
     * @throws \Exception
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        /** @var User $user */
        $user = $event->getUser();
        $payload = $event->getData();

        $data = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'ip' => $request->getClientIp(),
            'expired' => $user->isExpired($this->passwordIntervalDays),
        ];

        $event->setData(array_merge($data, $payload));
    }
}