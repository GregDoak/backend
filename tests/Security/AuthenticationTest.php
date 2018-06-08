<?php

namespace App\Tests\Security;

use App\Constant\AppConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Constant\Security\AuthenticationConstant;
use App\Constant\UrlConstant;
use App\Entity\Security\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTest extends WebTestCase
{
    private const USERNAME = 'TestUser';
    private const PASSWORD = 'TestPassword01'; //NOSONAR

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setUp(): void
    {
        parent::setUp();

        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        $systemUser = $userRepository->getUserByUsername(AppConstant::SYSTEM_USERNAME);
        $testUser = $userRepository->getUserByUsername(self::USERNAME);

        if ( ! $testUser instanceof User) {
            $client = $this->createClient();
            $encoder = $client->getContainer()->get('security.password_encoder');
            $roleRepository = $this->entityManager->getRepository(EntityConstant::ROLE);
            $role = $roleRepository->getRoleByTitle('ROLE_USER');

            $testUser = new User();
            $testUser
                ->setUsername(self::USERNAME)
                ->setPlainPassword($encoder->encodePassword($testUser, self::PASSWORD))
                ->setPassword($encoder->encodePassword($testUser, self::PASSWORD))
                ->setLoginCount()
                ->setRole($role)
                ->setEnabled(true)
                ->setCreatedBy($systemUser);

            $this->entityManager->persist($testUser);
            $this->entityManager->flush();
        }
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        $testUser = $userRepository->getUserByUsername(self::USERNAME);

        if ($testUser instanceof User) {
            $this->entityManager->remove($testUser);
            $this->entityManager->flush();
        }

        $this->entityManager->close();
        $this->entityManager = null;

    }

    public function testMissingCredentials(): void
    {
        $headers = [
            LabelConstant::CONTENT_TYPE => LabelConstant::JSON_TYPE,
        ];

        $this->client->request('POST', UrlConstant::LOGIN, [], [], $headers);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AuthenticationConstant::INVALID_CREDENTIALS, []);
    }

    public function testInvalidCredentials(): void
    {
        $headers = [
            LabelConstant::CONTENT_TYPE => LabelConstant::JSON_TYPE,
        ];

        $parameters = [
            'username' => self::USERNAME.'INVALID1',
            'password' => self::PASSWORD.'INVALID2',
        ];

        $this->client->request('POST', UrlConstant::LOGIN, $parameters, [], $headers);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AuthenticationConstant::INVALID_CREDENTIALS, []);
    }

    public function testMissingToken(): void
    {
        $this->client->request('GET', '/api/my/account');
        $this->doHeaderTests(Response::HTTP_FORBIDDEN);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AuthenticationConstant::JWT_NOT_FOUND, []);
    }

    public function testInvalidToken(): void
    {
        $headers = [
            LabelConstant::HTTP_AUTHORIZATION => 'Bearer INVALID',
            LabelConstant::CONTENT_TYPE => LabelConstant::JSON_TYPE,
        ];

        $this->client->request('GET', '/api/my/account', [], [], $headers);
        $this->doHeaderTests(Response::HTTP_FORBIDDEN);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AuthenticationConstant::JWT_INVALID, []);
    }

    public function testValidToken(): void
    {
        $headers = [
            LabelConstant::HTTP_AUTHORIZATION => 'Bearer '.$this->testValidCredentials(),
            LabelConstant::CONTENT_TYPE => LabelConstant::JSON_TYPE,
        ];

        $this->client->request('GET', '/api/my/account.json', [], [], $headers);
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doEntityTests(true, 1);
    }

    /**
     * @param string $return
     * @return string
     */
    public function testValidCredentials(string $return = 'token'): string
    {
        $headers = [
            LabelConstant::CONTENT_TYPE => LabelConstant::JSON_TYPE,
        ];

        $parameters = [
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
        ];

        $this->client->request('POST', UrlConstant::LOGIN, $parameters, [], $headers);
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doEntityTests(true, 2);
        $content = $this->getResponseContent();

        $this->assertTrue(isset($content->data->token), 'Token is not set.');
        $this->assertTrue(isset($content->data->refresh_token), 'refresh_token is not set.');

        return $return === 'token' ? $content->data->token : $content->data->refresh_token;
    }

    public function testMissingRefreshToken(): void
    {
        $this->client->request('POST', UrlConstant::REFRESH);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AuthenticationConstant::JWT_REFRESH_FAILED, []);
    }

    public function testInvalidRefreshToken(): void
    {
        $parameters = [
            LabelConstant::REFRESH_TOKEN => 'INVALID3',
        ];

        $this->client->request('POST', UrlConstant::REFRESH, $parameters);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AuthenticationConstant::JWT_REFRESH_FAILED, []);
    }

    public function testValidRefreshToken(): void
    {
        $parameters = [
            LabelConstant::REFRESH_TOKEN => $this->testValidCredentials(LabelConstant::REFRESH_TOKEN),
        ];

        $this->client->request('POST', UrlConstant::REFRESH, $parameters);
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doEntityTests(true, 1);
        $content = $this->getResponseContent();

        $this->assertTrue(isset($content->data->token), 'Token is not set.');
        $this->assertTrue(isset($content->data->refresh_token), 'refresh_token is not set.');
    }

    //testDisabledLogin
    //testDisabledToken
    //testUnauthorisedLogin
    //testUnauthorisedToken
}