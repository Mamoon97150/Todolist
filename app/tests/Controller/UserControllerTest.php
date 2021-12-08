<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;

    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testUserlistWorks(): void
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/UserListTestFixture.yaml'
        ]);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User1']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
    }

    public function testUserlistFails(): void
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/UserListTestFixture.yaml'
        ]);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User2']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/users');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateUserWorks()
    {
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            "user[username]" => "User4",
            "user[password][first]" => "password",
            "user[password][second]" => "password",
            "user[email]" => "user@fake.com"
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects("/");
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-success");
    }

    public function testCreateUserFails()
    {
        $crawler = $this->client->request('GET', '/users/create');
        $form = $crawler->selectButton('Ajouter')->form([
            "user[username]" => "",
            "user[password][first]" => "",
            "user[password][second]" => "",
            "user[email]" => ""
        ]);
        $this->client->submit($form);


        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testEditUserWorks()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/UserListTestFixture.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User1']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/users/2/edit');
        $form = $crawler->selectButton('Modifier')->form([
            "user[username]" => "Tester",
            "user[password][first]" => "password2",
            "user[password][second]" => "password2",
            "user[email]" => "user@fake.com"
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects("/");
        $this->client->followRedirect();
        $this->assertSelectorExists(".alert.alert-success");
    }

    public function testEditUserFails()
    {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/UserListTestFixture.yaml'
        ], false);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'User2']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/users/2/edit');
        $form = $crawler->selectButton('Modifier')->form([
            "user[username]" => "Test",
            "user[password][first]" => "",
            "user[password][second]" => "",
            "user[email]" => "user@fake.com"
        ]);
        $this->client->submit($form);


        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
