<?php

namespace App\Tests\Controller\Api\My;

use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private const TYPE = 'json';

    public function testGetEvents(): void
    {
        $url = '/api/my/similar-access-users.'.self::TYPE;
        $this->client->request('GET', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);

        $content = $this->getResponseContent();
        $this->assertTrue(
            $content->status,
            'Failed to get a list of Users'
        );
    }
}
