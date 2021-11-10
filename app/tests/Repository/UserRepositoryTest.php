<?php

namespace App\Tests\Repository;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{

    protected AbstractDatabaseTool $databaseTool;

    private EntityManager $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = self::bootKernel()->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testCount(){

        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/UserRepositoryTestFixtures.yaml'
        ], false);

        $users = $this->manager->getRepository(User::class)->count([]);

        $this->assertEquals(10, $users);
    }
}