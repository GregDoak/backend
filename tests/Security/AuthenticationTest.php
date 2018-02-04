<?php

namespace App\Tests\Security;

use App\Constant\Security\AuthenticationConstant;
use App\Entity\Security\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTest extends WebTestCase
{
    private const USERNAME = 'TestUser';
    private const PASSWORD = 'TestPassword01';

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setUp()
    {
        parent::setUp();

        $userRepository = $this->entityManager->getRepository('App:Security\User');

        $systemUser = $userRepository->getUserByUsername('system');
        $testUser = $userRepository->getUserByUsername(self::USERNAME);

        if ( ! $testUser instanceof User) {
            $client = $this->createClient();
            $encoder = $client->getContainer()->get('security.password_encoder');
            $roleRepository = $this->entityManager->getRepository('App:Security\Role');
            $role = $roleRepository->getRoleByTitle('ROLE_USER');

            $testUser = new User();
            $testUser
                ->setUsername(self::USERNAME)
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
    public function tearDown()
    {
        parent::tearDown();

        $userRepository = $this->entityManager->getRepository('App:Security\User');

        $testUser = $userRepository->getUserByUsername(self::USERNAME);

        if ($testUser instanceof User) {
            $this->entityManager->remove($testUser);
            $this->entityManager->flush();
        }

    }

    public function testMissingCredentials()
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $this->client->request('POST', '/api/authentication/login', [], [], $headers);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests('danger', AuthenticationConstant::INVALID_CREDENTIALS, []);
    }

    public function testInvalidCredentials()
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $parameters = [
            'username' => self::USERNAME.'INVALID',
            'password' => self::PASSWORD.'INVALID',
        ];

        $this->client->request('POST', '/api/authentication/login', $parameters, [], $headers);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests('danger', AuthenticationConstant::INVALID_CREDENTIALS, []);
    }

    public function testMissingToken()
    {
        $this->client->request('GET', '/api/my/account');
        $this->doHeaderTests(Response::HTTP_FORBIDDEN);
        $this->doMessageTests('danger', AuthenticationConstant::JWT_NOT_FOUND, []);
    }

    public function testInvalidToken()
    {
        $headers = [
            'HTTP_AUTHORIZATION' => 'Bearer INVALID',
            'CONTENT_TYPE' => 'application/json',
        ];

        $this->client->request('GET', '/api/my/account', [], [], $headers);
        $this->doHeaderTests(Response::HTTP_FORBIDDEN);
        $this->doMessageTests('danger', AuthenticationConstant::JWT_INVALID, []);
    }

    public function testValidToken()
    {
        $headers = [
            'HTTP_AUTHORIZATION' => 'Bearer '.$this->testValidCredentials(),
            'CONTENT_TYPE' => 'application/json',
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
            'CONTENT_TYPE' => 'application/json',
        ];

        $parameters = [
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
        ];

        $this->client->request('POST', '/api/authentication/login', $parameters, [], $headers);
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doEntityTests(true, 2);
        $content = $this->getResponseContent();

        $this->assertTrue(isset($content->data->token), 'Token is not set.');
        $this->assertTrue(isset($content->data->refresh_token), 'refresh_token is not set.');

        return $return === 'token' ? $content->data->token : $content->data->refresh_token;
    }

    public function testMissingRefreshToken()
    {
        $this->client->request('POST', '/api/authentication/refresh');
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests('danger', AuthenticationConstant::JWT_REFRESH_FAILED, []);
    }

    public function testInvalidRefreshToken()
    {
        $parameters = [
            'refresh_token' => 'INVALID',
        ];

        $this->client->request('POST', '/api/authentication/refresh', $parameters);
        $this->doHeaderTests(Response::HTTP_UNAUTHORIZED);
        $this->doMessageTests('danger', AuthenticationConstant::JWT_REFRESH_FAILED, []);
    }

    public function testValidRefreshToken()
    {
        $parameters = [
            'refresh_token' => $this->testValidCredentials('refresh_token'),
        ];

        $this->client->request('POST', '/api/authentication/refresh', $parameters);
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