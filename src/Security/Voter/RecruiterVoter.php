<?php

namespace App\Security\Voter;

use App\Entity\Recruiter;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class RecruiterVoter extends Voter
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['RECRUITER_EDIT', 'RECRUITER_VIEW'])
            && $subject instanceof \App\Entity\Recruiter;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'RECRUITER_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($subject, $user);
                break;
            case 'RECRUITER_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }

    private function canEdit(Recruiter $recruiter, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        if ($user === $recruiter->getUser()) {
            return true;
        }

        return false;
    }

    private function canDelete(Recruiter $recruiter, User $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

}
