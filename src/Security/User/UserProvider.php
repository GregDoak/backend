<?php

namespace App\Security\User;

use App\Constant\Security\AuthenticationConstant;
use App\Entity\Security\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package App\Security\User
 */
class UserProvider implements UserProviderInterface
{
    private $entityManager;

    /**
     * UserProvider constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserInterface $user
     * @throws UnsupportedUserException
     * @throws CustomUserMessageAuthenticationException
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if ( ! $user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', \get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $username
     * @throws CustomUserMessageAuthenticationException
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        $userRepository = $this->entityManager->getRepository('App:Security\User');

        $user = $userRepository->findOneBy(['username' => $username]);

        if ( ! $user instanceof User) {
            throw new CustomUserMessageAuthenticationException(
                AuthenticationConstant::INVALID_CREDENTIALS, [], 401
            );
        }

        if ( ! $user->isEnabled()) {
            throw new CustomUserMessageAuthenticationException(
                AuthenticationConstant::DISABLED_ACCOUNT, [], 401
            );
        }

        if ( ! $user->isAuthorised()) {
            throw new CustomUserMessageAuthenticationException(
                AuthenticationConstant::UNAUTHORISED_ACCOUNT, [], 401
            );
        }

        return $user;
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === 'App\Entity\Security\User';
    }
}