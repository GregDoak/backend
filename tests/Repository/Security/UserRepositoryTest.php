<?php

namespace App\Tests\Repository;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest
 * @package App\Tests\Repository
 */
class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    public function testGetRoleByTitleInvalid()
    {
        $user = $this->entityManager->getRepository('App:Security\User')->getUserByUsername('INVALID');

        $this->assertNull($user, 'The invalid user should be null');
    }

    public function testGetRoleByTitleValid()
    {
        $user = $this->entityManager->getRepository('App:Security\User')->getUserByUsername('system');

        $this->assertInstanceOf('App\Entity\Security\User', $user, 'The valid user should be an instance of a user');
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