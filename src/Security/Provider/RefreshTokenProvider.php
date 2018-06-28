<?php

namespace App\Security\Provider;

use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class RefreshTokenProvider
 * @package App\Security\Provider
 */
class RefreshTokenProvider implements UserProviderInterface
{
    protected $refreshTokenManager;
    protected $customUserProvider;

    /**
     * RefreshTokenProvider constructor.
     * @param RefreshTokenManagerInterface $refreshTokenManager
     */
    public function __construct(RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->refreshTokenManager = $refreshTokenManager;
    }

    /**
     * @param UserProviderInterface $customUserProvider
     */
    public function setCustomUserProvider(UserProviderInterface $customUserProvider): void
    {
        $this->customUserProvider = $customUserProvider;
    }

    /**
     * @param $token
     * @return null|string
     */
    public function getUsernameForRefreshToken($token): ?string
    {
        $token = $token ?? '';
        $refreshToken = $this->refreshTokenManager->get($token);

        if ($refreshToken instanceof RefreshTokenInterface) {
            return $refreshToken->getUsername();
        }

        return null;
    }

    /**
     * @param string $username
     * @throws UsernameNotFoundException
     * @throws \InvalidArgumentException
     * @return UserInterface
     */
    public function loadUserByUsername($username): UserInterface
    {
        if ($this->customUserProvider instanceof UserProviderInterface) {
            return $this->customUserProvider->loadUserByUsername($username);
        }

        return new User($username, null);
    }

    /**
     * @param UserInterface $user
     * @throws UnsupportedUserException
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if ($this->customUserProvider instanceof UserProviderInterface) {
            return $this->customUserProvider->refreshUser($user);
        }

        throw new UnsupportedUserException('There was a problem authenticating your user account.');
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class): bool
    {
        if ($this->customUserProvider instanceof UserProviderInterface) {
            return $this->customUserProvider->supportsClass($class);
        }

        return 'App\Entity\Security\User' === $class;
    }
}
