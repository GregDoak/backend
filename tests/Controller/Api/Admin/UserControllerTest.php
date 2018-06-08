<?php

namespace App\Tests\Controller\Api\Admin;

use App\Constant\AppConstant;
use App\Constant\Admin\UserConstant;
use App\Entity\Security\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    public function testGetUsers(): void
    {
        $this->client->request('GET', '/api/admin/users.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);

        $content = $this->getResponseContent();
        $this->assertTrue(
            $content->count > 0,
            'There are no users. Try running php bin\console app:initialise'
        );
    }

    public function testGetMissingUser(): void
    {
        $this->client->request('GET', '/api/admin/role/.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testGetInvalidUser(): void
    {
        $this->client->request('GET', '/api/admin/role/INVALID.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testGetValidUser(): void
    {
        $userRepository = $this->entityManager->getRepository('App:Security\User');

        /** @var User $user */
        $user = $userRepository->getUserByUsername('system');

        $this->assertNotNull(
            $user,
            'The system user was not found in the database.  Try running php bin\console app:initialise'
        );

        $this->client->request('GET', '/api/admin/user/'.$user->getId().'.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doEntityTests(true, 1);

        $content = $this->getResponseContent();
        $this->assertEquals($content->data->username, $user->getUsername(), 'The user does not match.');
    }

    public function testCreateMissingUser(): void
    {
        $this->client->request('POST', '/api/admin/user.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            UserConstant::CREATE_VALIDATION_ERROR,
            [
                UserConstant::USERNAME_EMPTY_ERROR,
                UserConstant::PASSWORD_EMPTY_ERROR,
            ]
        );
    }

    public function testCreateInvalidUser(): void
    {
        $parameters = [
            'username' => 'TEST_USER',
            'password' => 'short',
            'roles' => 'ROLE_INVALID',
        ];
        $this->client->request('POST', '/api/admin/user.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            UserConstant::CREATE_VALIDATION_ERROR,
            [
                sprintf(AppConstant::convertStringToSprintF(UserConstant::PASSWORD_MIN_LENGTH_ERROR), 8),
            ]
        );
    }

    public function testCreateValidUser(): void
    {
        $parameters = [
            'username' => 'TEST_USER',
            'password' => 'C0mplic@t3dP455w0rd!',
            'roles' => 'ROLE_USER',
        ];
        $this->client->request('POST', '/api/admin/user.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doMessageTests(
            'success',
            sprintf(
                AppConstant::convertStringToSprintF(UserConstant::CREATE_SUCCESS_MESSAGE),
                $parameters['username']
            ),
            []
        );
    }

    public function testCreateDuplicateUser(): void
    {
        $parameters = [
            'username' => 'TEST_USER',
            'password' => 'C0mplic@t3dP455w0rd!',
            'roles' => 'ROLE_USER',
        ];
        $this->client->request('POST', '/api/admin/user.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            UserConstant::CREATE_VALIDATION_ERROR,
            [
                sprintf(
                    AppConstant::convertStringToSprintF(UserConstant::UNIQUE_ENTITY_ERROR),
                    '"'.$parameters['username'].'"'
                ),
            ]
        );
    }

    public function testUpdateMissingUser(): void
    {
    }

    public function testUpdateInvalidUser(): void
    {
    }

    public function testUpdateDuplicateUser(): void
    {
    }

    public function testUpdateValidUser(): void
    {
    }

    public function testDeleteMissingUser(): void
    {
        $this->client->request('DELETE', '/api/admin/user/.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testDeleteInvalidUser(): void
    {
        $this->client->request('PUT', '/api/admin/user/INVALID.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testDeleteValidUser(): void
    {
        $userRepository = $this->entityManager->getRepository('App:Security\User');

        /** @var User $role */
        $user = $userRepository->getUserByUsername('TEST_USER');

        $this->assertNotNull(
            $user,
            'The TEST_USER was not found in the database.  Try running php bin\console app:initialise'
        );

        $this->client->request('DELETE', '/api/admin/user/'.$user->getId().'.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            'success',
            sprintf(UserConstant::DELETE_SUCCESS_MESSAGE, $user->getUsername()),
            []
        );
    }
}