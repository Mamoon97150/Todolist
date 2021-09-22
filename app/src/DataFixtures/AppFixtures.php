<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * AppFixtures constructor.
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $anon = new User();
        $anon->setUsername("Anonymous")
            ->setEmail("anonymous@fake.com")
            ->setPassword($this->passwordHasher->hashPassword($anon, ""))
        ;
        $manager->persist($anon);


        for ($i = 0; $i < 9; $i++) {
            $user = new User();
            $user->setUsername(sprintf("User%d", $i))
                ->setEmail(sprintf("user%d", $i)."@fake.com")
                ->setPassword($this->passwordHasher->hashPassword($user, "password"))
            ;
            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i < 20; $i++) {
            $task = new Task();
            $task->setTitle(sprintf("Task %d", $i+1))
                ->setContent(sprintf("Content for Task %d", $i+1))
                ->setAuthor($anon);

            $manager->persist($task);
        }
        $manager->flush();
    }
}
