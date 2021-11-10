<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use function PHPUnit\Framework\isNull;

class PasswordHasher
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user, LifecycleEventArgs $event){

        $hashed = $this->hasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashed);

        return $user;
    }

    public function preUpdate(User $user, LifecycleEventArgs $event){

        if (!isNull($user->getPassword())){
            $hashed = $this->hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashed);
        }

        return $user;
    }
}