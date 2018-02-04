<?php

namespace App\Tests\Controller\Api\Admin;

use App\Constant\AppConstant;
use App\Constant\Admin\RoleConstant;
use App\Entity\Security\Role;
use App\Tests\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RoleControllerTest extends WebTestCase
{
    public function testGetRoles()
    {
        $this->client->request('GET', '/api/admin/roles.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);

        $content = $this->getResponseContent();
        $this->assertTrue(
            $content->count > 0,
            'There are no security roles. Try running php bin\console app:initialise'
        );
    }

    public function testGetInvalidRole()
    {
        $this->client->request('GET', '/api/admin/role/INVALID.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests('danger', AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testGetValidRole()
    {
        $roleRepository = $this->entityManager->getRepository('App:Security\Role');

        /** @var Role $role */
        $role = $roleRepository->getRoleByTitle('ROLE_USER');

        $this->assertNotNull(
            $role,
            'The ROLE_USER was not found in the database.  Try running php bin\console app:initialise'
        );

        $this->client->request('GET', '/api/admin/role/'.$role->getId().'.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doEntityTests(true, 1);

        $content = $this->getResponseContent();
        $this->assertEquals($content->data->title, $role->getTitle(), 'The roles do not match.');
    }

    public function testCreateInvalidRole()
    {
        $parameters = [
            'title' => '',
            'description' => null,
        ];

        $this->client->request('POST', '/api/admin/role.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            'danger',
            RoleConstant::CREATE_VALIDATION_ERROR,
            [
                RoleConstant::TITLE_EMPTY_ERROR,
                RoleConstant::DESCRIPTION_EMPTY_ERROR,
            ]
        );
    }

    public function testCreateRole()
    {
        $parameters = [
            'title' => 'ROLE_TEST',
            'description' => 'This is a test role and has no access.',
        ];

        $this->client->request('POST', '/api/admin/role.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_CREATED);
        $this->doMessageTests(
            'success',
            sprintf(AppConstant::convertStringToSprintF(RoleConstant::CREATE_SUCCESS_MESSAGE), $parameters['title']),
            []
        );
    }

    public function testCreateDuplicateRole()
    {
        $parameters = [
            'title' => 'ROLE_TEST',
            'description' => 'This is a test role and has no access.',
        ];

        $this->client->request('POST', '/api/admin/role.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            'danger',
            RoleConstant::CREATE_VALIDATION_ERROR,
            [
                sprintf(
                    AppConstant::convertStringToSprintF(RoleConstant::UNIQUE_ENTITY_ERROR),
                    '"'.$parameters['title'].'"'
                ),
            ]
        );
    }

    public function testUpdateMissingRole()
    {
        $this->client->request('PUT', '/api/admin/role/.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests('danger', AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testUpdateInvalidRole()
    {
        $parameters = [
            'title' => 'ROLE_TEST',
            'description' => 'This is a test role and has no access.',
        ];

        $this->client->request('PUT', '/api/admin/role/INVALID.json', $parameters, [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests('danger', AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testUpdateDuplicateRole()
    {
        $roleRepository = $this->entityManager->getRepository('App:Security\Role');

        /** @var Role $role */
        $role = $roleRepository->getRoleByTitle('ROLE_TEST');

        $this->assertNotNull(
            $role,
            'The ROLE_TEST was not found in the database.  Try running php bin\console app:initialise'
        );

        $parameters = [
            'title' => 'ROLE_USER',
            'description' => 'This is a test role and has no access.',
        ];

        $this->client->request(
            'PUT',
            '/api/admin/role/'.$role->getId().'.json',
            $parameters,
            [],
            $this->getJsonHeaders()
        );

        $this->doHeaderTests(Response::HTTP_BAD_REQUEST);
        $this->doMessageTests(
            'danger',
            RoleConstant::UPDATE_VALIDATION_ERROR,
            [
                sprintf(
                    AppConstant::convertStringToSprintF(RoleConstant::UNIQUE_ENTITY_ERROR),
                    '"'.$parameters['title'].'"'
                ),
            ]
        );
    }

    public function testUpdateRole()
    {
        $roleRepository = $this->entityManager->getRepository('App:Security\Role');

        /** @var Role $role */
        $role = $roleRepository->getRoleByTitle('ROLE_TEST');

        $this->assertNotNull(
            $role,
            'The ROLE_TEST was not found in the database.  Try running php bin\console app:initialise'
        );

        $parameters = [
            'title' => 'ROLE_TEST_UPDATE',
            'description' => 'This is a test role and has no access.',
        ];

        $this->client->request(
            'PUT',
            '/api/admin/role/'.$role->getId().'.json',
            $parameters,
            [],
            $this->getJsonHeaders()
        );
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            'success',
            sprintf(RoleConstant::UPDATE_SUCCESS_MESSAGE, $role->getTitle()),
            []
        );
    }

    public function testDeleteMissingRole()
    {
        $this->client->request('DELETE', '/api/admin/role/.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests('danger', AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testDeleteInvalidRole()
    {
        $this->client->request('PUT', '/api/admin/role/INVALID.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_NOT_FOUND);
        $this->doMessageTests('danger', AppConstant::HTTP_NOT_FOUND, []);
    }

    public function testDeleteRole()
    {
        $roleRepository = $this->entityManager->getRepository('App:Security\Role');

        /** @var Role $role */
        $role = $roleRepository->getRoleByTitle('ROLE_TEST_UPDATE');

        $this->assertNotNull(
            $role,
            'The ROLE_TEST_UPDATE was not found in the database.  Try running php bin\console app:initialise'
        );

        $this->client->request('DELETE', '/api/admin/role/'.$role->getId().'.json', [], [], $this->getJsonHeaders());
        $this->doHeaderTests(Response::HTTP_OK);
        $this->doMessageTests(
            'success',
            sprintf(RoleConstant::DELETE_SUCCESS_MESSAGE, $role->getTitle()),
            []
        );
    }

}