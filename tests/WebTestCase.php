<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class WebTestCase
 * @package App\Tests
 */
class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    public $authenticationToken;
    /** @var Client $client */
    public $client;
    /** @var EntityManager $entityManager */
    public $entityManager;
    /** @var ValidatorInterface $validator */
    public $validator;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->authenticationToken = $this->getAuthenticationToken();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->validator = $this->client->getContainer()->get('validator');
    }

    /**
     * @return null|string
     */
    public function getAuthenticationToken(): ?string
    {
        $username = getenv('APP_DEFAULT_USERNAME');
        $password = getenv('APP_DEFAULT_PASSWORD');
        $token = null;

        $parameters = [
            'username' => $username,
            'password' => $password,
        ];

        $this->client->request('POST', '/api/authentication/login', $parameters);
        $response = $this->client->getResponse();

        if ($response !== null) {
            $content = json_decode($response->getContent());

            $token = $content->data->token;
        }

        return $token;
    }

    public function tearDown()
    {
        unset($this->authenticationToken, $this->client);
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @param int $httpResponseCode
     */
    public function doHeaderTests(int $httpResponseCode): void
    {
        $response = $this->client->getResponse();
        $this->assertNotNull($response, 'Response is null');
        $this->assertEquals($httpResponseCode, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), 'Invalid JSON response.');
    }

    /**
     * @param bool $expectedStatus
     * @param int $expectedCount
     */
    public function doEntityTests(bool $expectedStatus, int $expectedCount): void
    {
        $content = $this->getResponseContent();
        $this->assertEquals($expectedStatus, $content->status, 'The status does not match.');
        $this->assertEquals($expectedCount, $content->count, 'The count totals do not match');
    }

    /**
     * @return mixed
     */
    public function getResponseContent()
    {
        $response = $this->client->getResponse() ?? [];

        return json_decode($response->getContent());
    }

    /**
     * @param string $expectedType
     * @param string $expectedMessage
     * @param array $expectedMessages
     */
    public function doMessageTests(
        string $expectedType,
        string $expectedMessage,
        array $expectedMessages = []
    ): void {
        $content = $this->getResponseContent();
        $this->assertEquals(\count((array)$content->data), $content->count, 'The count totals do not match.');
        $this->assertEquals($expectedType, $content->data->type, 'The message type is incorrect.');
        $this->assertEquals($expectedMessage, $content->data->message, 'The message is incorrect');
        $this->assertEquals($expectedMessages, $content->data->messages, 'The messages is incorrect.');
    }

    /**
     * @return array
     */
    public function getJsonHeaders(): array
    {
        return [
            'HTTP_AUTHORIZATION' => 'Bearer '.$this->authenticationToken,
            'CONTENT_TYPE' => 'application/json',
        ];
    }
}