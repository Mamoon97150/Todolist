<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{

    //Rajouter la manip de test (KERNAL_CLASS) dans readme
    protected AbstractDatabaseTool $databaseTool;

    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }


    public function testTaskListWorks()
    {
        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskListFails()
    {
        $this->client->request('GET', '/task');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskCreationWorks()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/TaskControllerTestFixtures.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User1']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            "task[title]" => "Task test 2",
            "task[content]" => "Content for task test 2",
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-success");
    }

    //todo: check why fails
    public function testTaskCreationFails()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/TaskControllerTestFixtures.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User1']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            "task[title]" => "",
            "task[content]" => ""
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    public function testTaskModificationWorks()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/TaskControllerTestFixtures.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User0']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $form = $crawler->selectButton('Modifier')->form([
            "task[title]" => "Task test 2",
            "task[content]" => "Content for task test 2"
        ]);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-success");


    }



    public function testTaskModificationFails()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/TaskControllerTestFixtures.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User0']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/1/edit');
        $form = $crawler->selectButton('Modifier')->form([
            "task[title]" => ""
        ]);
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    //TODO: finish addinf fail scenario
    public function testTaskToggleWorks()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/OneTaskTestFixture.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks');

        $crawler = $this->client->submitForm('Marquer comme faite');

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-success");
    }


    public function testTaskDeletionWorks()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/OneTaskTestFixture.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks');

        $crawler = $this->client->submitForm('Supprimer');

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-success");

    }

    public function testTaskDeletionFails()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/OneTaskTestFixture.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User2']);
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks');

        $crawler = $this->client->submitForm('Supprimer');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

    }
}