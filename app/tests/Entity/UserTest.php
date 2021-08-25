<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{

    //TODO: validator dependency injection
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function getUserEntity(): User
    {
        return $user = (new User())
            ->setUsername("Tester")
            ->setEmail("test@test.com")
            ->setPassword("password")
        ;
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($user);
        $message = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error){
            $message[] = $error->getPropertyPath().' => '.$error->getMessage();
        }

        $this->assertCount($number, $errors, implode(',', $message));
    }

    public function testValidUserEntity() {
        $this->assertHasErrors($this->getUserEntity());
    }

    public function testInvalidUserEntity() {
        $this->assertHasErrors($this->getUserEntity()->setUsername('Test'), 1);
        $this->assertHasErrors($this->getUserEntity()->setEmail('Test.com'), 1);
    }

    public function testBlankUserEntity() {
        $this->assertHasErrors($this->getUserEntity()->setUsername(''), 2);
        $this->assertHasErrors($this->getUserEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getUserEntity()->setPassword(''), 2);

    }

    public function testNonUniqueUserEntity() {
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__).'/Fixtures/UniqueUserFixtures.yaml'
        ]);

        $this->assertHasErrors($this->getUserEntity()->setUsername('User0'), 1);
        $this->assertHasErrors($this->getUserEntity()->setEmail('user0@fake.com'), 1);
    }

}