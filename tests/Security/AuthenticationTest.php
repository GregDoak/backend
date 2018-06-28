<?php

namespace App\Tests\Security;

use App\Constant\AppConstant;
use App\Constant\LabelConstant;
use App\Constant\Security\AuthenticationConstant;
use App\Constant\UrlConstant;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTest extends WebTestCase
{
    public function testInvalidCredentials(): void
    {
        $headers = [
            LabelConstant::CONTENT_TYPE => LabelConstant::JSON_TYPE,
        ];

        $parameters = [
            'username' => 'INVALID_USERNAME',
            'password' => 'INVALID_PASSWORD',
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
            'username' => getenv('APP_DEFAULT_USERNAME'),
            'password' => getenv('APP_DEFAULT_PASSWORD'),
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
            LabelConstant::REFRESH_TOKEN => 'INVALID_TOKEN',
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
}