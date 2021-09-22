<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    const EDIT = 'edit';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject)
    {
        //if the attribute isn't one we support, return false
        if ($attribute != self::EDIT) {
            return false;
        }

        // only vote on `User` objects
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        /** @var User $userDetails */
        $userDetails = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($userDetails, $user);
        }

    }

    private function canEdit(User $userDetails, UserInterface|User $user): bool
    {
        return true;
    }
}