<?php

namespace App\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RoleRepositoryTest
 * @package App\Tests\Repository
 */
class RoleRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    public function testGetRoleByTitleInvalid(): void
    {
        $role = $this->entityManager->getRepository('App:Security\Role')->getRoleByTitle('INVALID');

        $this->assertNull($role, 'The invalid role should be null');
    }

    public function testGetRoleByTitleValid(): void
    {
        $role = $this->entityManager->getRepository('App:Security\Role')->getRoleByTitle('ROLE_USER');

        $this->assertInstanceOf('App\Entity\Security\Role', $role, 'The valid role should be an instance of a role');
    }

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}