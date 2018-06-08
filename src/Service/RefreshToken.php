<?php

namespace App\Service;

use App\Constant\Security\AuthenticationConstant;
use App\Entity\Security\JwtRefreshToken;
use App\Security\Authenticator\RefreshTokenAuthenticator;
use App\Security\Http\Authentication\AuthenticationSuccessHandler;
use App\Security\Provider\RefreshTokenProvider;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use UAParser\Exception\FileNotFoundException;
use UAParser\Parser;

/**
 * Class RefreshToken
 * @package App\Service
 */
class RefreshToken
{
    private $authenticator;
    private $provider;
    private $successHandler;
    private $failureHandler;
    private $refreshTokenManager;
    private $providerKey;
    private $ttl;
    private $ttlUpdate;

    /**
     * RefreshToken constructor.
     * @param RefreshTokenAuthenticator $authenticator
     * @param RefreshTokenProvider $provider
     * @param AuthenticationSuccessHandler $successHandler
     * @param AuthenticationFailureHandler $failureHandler
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @param $ttl
     * @param $providerKey
     * @param $ttlUpdate
     */
    public function __construct( //NOSONAR
        RefreshTokenAuthenticator $authenticator,
        RefreshTokenProvider $provider,
        AuthenticationSuccessHandler $successHandler,
        AuthenticationFailureHandler $failureHandler,
        RefreshTokenManagerInterface $refreshTokenManager,
        $ttl,
        $providerKey,
        $ttlUpdate
    ) {
        $this->authenticator = $authenticator;
        $this->provider = $provider;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->ttl = $ttl;
        $this->providerKey = $providerKey;
        $this->ttlUpdate = $ttlUpdate;
    }

    /**
     * @param Request $request
     * @throws \InvalidArgumentException
     * @throws CustomUserMessageAuthenticationException
     * @throws FileNotFoundException
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function refresh(Request $request)
    {
        try {
            $preAuthenticatedToken = $this->authenticator->authenticateToken(
                $this->authenticator->createToken($request, $this->providerKey),
                $this->provider,
                $this->providerKey
            );
        } catch (AuthenticationException $exception) {
            return $this->failureHandler->onAuthenticationFailure($request, $exception);
        }

        $refreshToken = $this->refreshTokenManager->get($preAuthenticatedToken->getCredentials());

        if (null === $refreshToken || ! $refreshToken->isValid()) {
            return $this->failureHandler->onAuthenticationFailure(
                $request,
                new AuthenticationException(
                    AuthenticationConstant::JWT_REFRESH_FAILED
                )
            );
        }

        if ($this->ttlUpdate && $refreshToken instanceof JwtRefreshToken) {
            $expirationDate = new \DateTime();
            $expirationDate->modify(sprintf('+%d seconds', $this->ttl));

            $parser = Parser::create();
            $results = $parser->parse($request->headers->get('User-Agent'));

            $refreshToken
                ->setValid($expirationDate)
                ->setUpdatedOn()
                ->setOperatingSystem($results->os->family)
                ->setBrowser($results->ua->family);

            $this->refreshTokenManager->save($refreshToken);
        }

        return $this->successHandler->onAuthenticationSuccess($request, $preAuthenticatedToken);
    }
}
