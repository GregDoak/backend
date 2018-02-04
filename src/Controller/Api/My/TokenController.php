<?php

namespace App\Controller\Api\My;

use App\Constant\My\TokenConstant;
use App\Controller\Api\ApiController;
use App\Entity\Security\JwtRefreshToken;
use App\Helper\ResponseHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TokenController extends ApiController
{
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * @Rest\Get("/my/tokens.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=TOKEN_GET_TOKENS_SECURITY_ERROR)
     * @return View
     */
    public function getTokens(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');
        $tokenRepository = $this->entityManager->getRepository('App:Security\JwtRefreshToken');

        $tokens = $tokenRepository->getTokens($this->authenticatedUser->getUsername());

        $data = ResponseHelper::buildSuccessResponse(200, $tokens);

        ResponseHelper::logResponse(TokenConstant::GET_MULTIPLE_SUCCESS_LOG, $data, $this);

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Delete("/my/tokens.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=TOKEN_DELETE_TOKENS_SECURITY_ERROR)
     * @return View
     */
    public function deleteTokens(): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->delete('Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken', 't')
            ->where('t.username = :username')
            ->setParameter(':username', $this->authenticatedUser->getUsername());

        $queryBuilder->getQuery()->execute();

        $data = ResponseHelper::buildMessageResponse('success', TokenConstant::DELETE_TOKENS_SUCCESS_MESSAGE);

        $data = ResponseHelper::buildSuccessResponse(200, $data);

        ResponseHelper::logResponse(
            sprintf(TokenConstant::DELETE_TOKENS_SUCCESS_LOG, $this->authenticatedUser->getUsername()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);
    }

    /**
     * @Rest\Delete("/my/token/{id}.{_format}", defaults={"_format"="json"})
     * @Security("has_role('ROLE_USER')", message=TOKEN_DELETE_TOKEN_SECURITY_ERROR)
     * @ParamConverter("jwtRefreshToken", class="App\Entity\Security\JwtRefreshToken", options={"id" = "id"})
     * @param JwtRefreshToken $jwtRefreshToken
     * @throws ORMException
     * @return View
     */
    public function deleteToken(JwtRefreshToken $jwtRefreshToken): View
    {
        $this->authenticatedUser = $this->getUser();
        $this->entityManager = $this->get('doctrine.orm.entity_manager');

        if ($jwtRefreshToken->getUsername() !== $this->authenticatedUser->getUsername()) {
            throw new AccessDeniedHttpException(TokenConstant::DELETE_TOKEN_SECURITY_ERROR);
        }

        $this->entityManager->remove($jwtRefreshToken);
        $this->entityManager->flush();

        $data = ResponseHelper::buildMessageResponse('success', TokenConstant::DELETE_TOKEN_SUCCESS_MESSAGE);

        $data = ResponseHelper::buildSuccessResponse(200, $data);

        ResponseHelper::logResponse(
            sprintf(TokenConstant::DELETE_TOKEN_SUCCESS_LOG, $this->authenticatedUser->getUsername()),
            $data,
            $this
        );

        return $this->view($data, $data['code']);

    }
}