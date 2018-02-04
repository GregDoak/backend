<?php

namespace App\Security\Http\Authentication;

use App\Entity\Security\User;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

/**
 * Class AuthenticationSuccessHandler
 * @package PortalBundle\Security\Http\Authentication
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected $dispatcher;
    protected $entityManager;
    protected $jwtManager;

    /**
     * @param JWTManager $jwtManager
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManager $entityManager
     */
    public function __construct(
        JWTManager $jwtManager,
        EventDispatcherInterface $dispatcher,
        EntityManager $entityManager
    ) {
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @throws CustomUserMessageAuthenticationException
     * @return JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var User $user */
        $user = $token->getUser();
        $jwt = $this->jwtManager->create($user);

        $response = new JsonResponse();
        $event = new AuthenticationSuccessEvent(['token' => $jwt], $user, $response);

        $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);
        try {

            $userRepository = $this->entityManager->getRepository('App:Security\User');
            $user = $userRepository->getUserByUsername($user->getUsername());

            $user
                ->setLastLogin()
                ->setLoginCount();

            $response->setStatusCode(201);
            $response->setData($event->getData());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw new CustomUserMessageAuthenticationException(
                'Your login attempt was successful but a problem has prevented you from logging in.', [], 500
            );
        }

        return $response;
    }
}
