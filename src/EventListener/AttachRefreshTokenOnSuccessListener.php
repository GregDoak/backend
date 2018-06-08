<?php

namespace App\EventListener;

use App\Constant\EntityConstant;
use App\Entity\Security\JwtRefreshToken;
use Doctrine\ORM\EntityManager;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Request\RequestRefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UAParser\Exception\FileNotFoundException;
use UAParser\Parser;

/**
 * Class AttachRefreshTokenOnSuccessListener
 * @package App\EventListener
 */
class AttachRefreshTokenOnSuccessListener
{
    protected $refreshTokenManager;
    protected $userRefreshTokenManager;
    protected $ttl;
    protected $validator;
    protected $requestStack;
    protected $entityManager;

    /**
     * AttachRefreshTokenOnSuccessListener constructor.
     * @param RefreshTokenManagerInterface $refreshTokenManager
     * @param int $ttl
     * @param ValidatorInterface $validator
     * @param RequestStack $requestStack
     * @param EntityManager $entityManager
     */
    public function __construct(
        RefreshTokenManagerInterface $refreshTokenManager,
        int $ttl,
        ValidatorInterface $validator,
        RequestStack $requestStack,
        EntityManager $entityManager
    ) {
        $this->refreshTokenManager = $refreshTokenManager;
        $this->ttl = $ttl;
        $this->validator = $validator;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function attachRefreshToken(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();
        $request = $this->requestStack->getCurrentRequest();

        if ( ! $user instanceof UserInterface || ! $request instanceof Request) {
            return;
        }

        $refreshTokenString = RequestRefreshToken::getRefreshToken($request);

        if ($refreshTokenString) {
            $data['data']['refresh_token'] = $refreshTokenString;
        } else {
            $refreshToken = $this->generateToken($request, $user);
            $refreshToken = $this->validateToken($refreshToken);

            $this->refreshTokenManager->save($refreshToken);
            $data['data']['refresh_token'] = $refreshToken->getRefreshToken();
            $data['count']++;
        }

        $event->setData($data);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return JwtRefreshToken
     * @throws FileNotFoundException
     * @throws \Exception
     */
    private function generateToken(Request $request, UserInterface $user): JwtRefreshToken
    {
        $datetime = new \DateTime();
        $datetime->modify('+'.$this->ttl.' seconds');

        $parser = Parser::create();
        $results = $parser->parse($request->headers->get('User-Agent'));

        $tokenRepository = $this->entityManager->getRepository(EntityConstant::JWT_REFRESH_TOKEN);
        /** @var JwtRefreshToken $refreshToken */
        $refreshToken = $this->refreshTokenManager->create();
        $refreshToken
            ->setUsername($user->getUsername())
            ->setOperatingSystem($results->os->family)
            ->setBrowser($results->ua->family);

        $refreshToken = $tokenRepository->getToken($refreshToken);

        if ( ! $refreshToken instanceof JwtRefreshToken) {
            $refreshToken = $this->refreshTokenManager->create();

            $refreshToken
                ->setUsername($user->getUsername())
                ->setRefreshToken()
                ->setOperatingSystem($results->os->family)
                ->setBrowser($results->ua->family);
        }

        $refreshToken->setValid($datetime);

        return $refreshToken;
    }

    /**
     * @param JwtRefreshToken $refreshToken
     * @return JwtRefreshToken
     * @throws \Exception
     */
    private function validateToken(JwtRefreshToken $refreshToken): JwtRefreshToken
    {
        $valid = false;
        while ($valid === false) {
            $valid = true;
            $errors = $this->validator->validate($refreshToken);
            if ($errors->count() > 0) {
                foreach ($errors as $error) {
                    if ('refreshToken' === $error->getPropertyPath()) {
                        $valid = false;
                        $refreshToken->setRefreshToken();
                    }
                }
            }
        }

        return $refreshToken;
    }
}
