<?php

namespace App\Tests\Controller\Api\Admin;

use App\Constant\AppConstant;
use App\Constant\Admin\UserConstant;
use App\Constant\EntityConstant;
use App\Constant\LabelConstant;
use App\Entity\Security\User;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private const API_URL_SINGLE = '/api/admin/user';
    private const TYPE = 'json';
    private const USERNAME = 'TEST_USER';

    public function testGetUsers(): void
    {
        $url = '/api/admin/users.'.self::TYPE;
        $this->client->request('GET', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);

        $content = $this->getResponseContent();
        $this->assertTrue(
            $content->count > 0,
            'There are no users. Try running php bin\console app:initialise'
        );
    }

    public function testGetMissingUser(): void
    {
        $url = self::API_URL_SINGLE.'/.'.self::TYPE;
        $this->client->request('GET', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testGetInvalidUser(): void
    {
        $url = self::API_URL_SINGLE.'/INVALID.'.self::TYPE;
        $this->client->request('GET', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testGetValidUser(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        /** @var User $user */
        $user = $userRepository->getUserByUsername('system');

        $this->assertNotNull(
            $user,
            'The system user was not found in the database.  Try running php bin\console app:initialise'
        );

        $url = self::API_URL_SINGLE.'/'.$user->getId().'.'.self::TYPE;
        $this->client->request('GET', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doEntityTests(true, 1);

        $content = $this->getResponseContent();
        $this->assertEquals($content->data->username, $user->getUsername(), 'The user does not match.');
    }

    public function testCreateMissingUser(): void
    {
        $url = self::API_URL_SINGLE.'.'.self::TYPE;
        $this->client->request('POST', $url, [], [], $this->getJsonHeaders());
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
            LabelConstant::USERNAME => self::USERNAME,
            LabelConstant::PASSWORD => 'short',
            LabelConstant::ROLES => 'ROLE_INVALID',
        ];
        $url = self::API_URL_SINGLE.'.'.self::TYPE;
        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
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
            LabelConstant::USERNAME => self::USERNAME,
            LabelConstant::PASSWORD => 'C0mplic@t3dP455w0rd!',
            LabelConstant::ROLES => 'ROLE_USER',
        ];
        $url = self::API_URL_SINGLE.'.'.self::TYPE;
        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doMessageTests(
            AppConstant::SUCCESS_TYPE,
            sprintf(
                AppConstant::convertStringToSprintF(UserConstant::CREATE_SUCCESS_MESSAGE),
                $parameters[LabelConstant::USERNAME]
            ),
            []
        );
    }

    public function testCreateDuplicateUser(): void
    {
        $parameters = [
            LabelConstant::USERNAME => self::USERNAME,
            LabelConstant::PASSWORD => 'C0mplic@t3dP455w0rd!',
            LabelConstant::ROLES => 'ROLE_USER',
        ];
        $url = self::API_URL_SINGLE.'.'.self::TYPE;
        $this->client->request('POST', $url, $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            UserConstant::CREATE_VALIDATION_ERROR,
            [
                sprintf(
                    AppConstant::convertStringToSprintF(UserConstant::UNIQUE_ENTITY_ERROR),
                    '"'.$parameters[LabelConstant::USERNAME].'"'
                ),
            ]
        );
    }

    public function testUpdateMissingUser(): void
    {
        $url = self::API_URL_SINGLE.'/.'.self::TYPE;
        $this->client->request('PUT', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testUpdateInvalidUser(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        /** @var User $user */
        $user = $userRepository->getUserByUsername(self::USERNAME);

        $parameters = [
            LabelConstant::USERNAME => 'a',
        ];
        $url = self::API_URL_SINGLE.'/'.$user->getId().'.'.self::TYPE;
        $this->client->request('PUT', $url, $parameters, [],
            $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            UserConstant::UPDATE_VALIDATION_ERROR,
            [
                sprintf(AppConstant::convertStringToSprintF(UserConstant::USERNAME_MIN_LENGTH_ERROR), 3),
            ]
        );
    }

    public function testUpdateDuplicateUser(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        /** @var User $user */
        $user = $userRepository->getUserByUsername(self::USERNAME);

        $parameters = [
            LabelConstant::USERNAME => 'system',
        ];
        $url = self::API_URL_SINGLE.'/'.$user->getId().'.'.self::TYPE;
        $this->client->request('PUT', $url, $parameters, [],
            $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            AppConstant::DANGER_TYPE,
            UserConstant::UPDATE_VALIDATION_ERROR,
            [
                sprintf(AppConstant::convertStringToSprintF(UserConstant::UNIQUE_ENTITY_ERROR),
                    '"'.$parameters[LabelConstant::USERNAME].'"'),
            ]
        );
    }

    public function testUpdateValidUser(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        /** @var User $user */
        $user = $userRepository->getUserByUsername(self::USERNAME);

        $parameters = [
            LabelConstant::USERNAME => $user->getUsername().'_UPDATE',
        ];
        $url = self::API_URL_SINGLE.'/'.$user->getId().'.'.self::TYPE;
        $this->client->request('PUT', $url, $parameters, [],
            $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            AppConstant::SUCCESS_TYPE,
            sprintf(UserConstant::UPDATE_SUCCESS_MESSAGE, $user->getUsername()),
            []
        );
    }

    public function testDeleteMissingUser(): void
    {
        $url = self::API_URL_SINGLE.'/.'.self::TYPE;
        $this->client->request('DELETE', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testDeleteInvalidUser(): void
    {
        $url = self::API_URL_SINGLE.'/INVALID.'.self::TYPE;
        $this->client->request('PUT', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests(AppConstant::DANGER_TYPE, AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testDeleteValidUser(): void
    {
        $userRepository = $this->entityManager->getRepository(EntityConstant::USER);

        /** @var User $role */
        $user = $userRepository->getUserByUsername('TEST_USER_UPDATE');

        $this->assertNotNull(
            $user,
            'The TEST_USER was not found in the database.  Try running php bin\console app:initialise'
        );

        $url = self::API_URL_SINGLE.'/'.$user->getId().'.'.self::TYPE;
        $this->client->request('DELETE', $url, [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            AppConstant::SUCCESS_TYPE,
            sprintf(UserConstant::DELETE_SUCCESS_MESSAGE, $user->getUsername()),
            []
        );
    }
}