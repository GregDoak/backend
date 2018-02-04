<?php

namespace App\Repository\Security;

use App\Entity\Security\JwtRefreshToken;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;

/**
 * Class JwtRefreshTokenRepository
 * @package App\Repository\Security
 */
class JwtRefreshTokenRepository extends RefreshTokenRepository
{

    /**
     * @param JwtRefreshToken $jwtRefreshToken
     * @return JwtRefreshToken|null
     */
    public function getToken(JwtRefreshToken $jwtRefreshToken): ?JwtRefreshToken
    {
        $token = null;

        $tokens = $this->createQueryBuilder('t')
            ->where('t.username = :username')
            ->andWhere('t.operatingSystem = :operatingSystem')
            ->andWhere('t.browser = :browser')
            ->setParameter(':username', $jwtRefreshToken->getUsername())
            ->setParameter(':operatingSystem', $jwtRefreshToken->getOperatingSystem())
            ->setParameter(':browser', $jwtRefreshToken->getBrowser())
            ->setMaxResults(3)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();

        if (\count($tokens) === 3) {
            $token = $tokens[0];
        }

        return $token;
    }

    public function getTokens($username)
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.username = :username')
            ->setParameter(':username', $username)
            ->orderBy('t.valid', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}