<?php

namespace App\Security\Voter;

use App\Entity\Candidate;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;


class CandidateVoter extends Voter
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
        return in_array($attribute, ['CANDIDATE_EDIT', 'CANDIDATE_VIEW'])
            && $subject instanceof \App\Entity\Candidate;
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
            case 'CANDIDATE_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                return $this->canEdit($subject, $user);
                break;
            case 'CANDIDATE_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canView($subject, $user);
                break;
        }

        return false;
    }

    private function canEdit(Candidate $candidate, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        return (!$user->getIsActived()); // on ne peut plus editer un user valider 
    }

    private function canView(Candidate $candidate, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        if( $user === $candidate->getUser()) {
            return true;
        }
        return false;
    }



    private function canDelete(Candidate $candidate, User $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

}
