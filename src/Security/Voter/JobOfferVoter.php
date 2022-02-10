<?php

namespace App\Security\Voter;
use App\Entity\JobOffer;
use App\Entity\User;
use App\Entity\Recruiter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;



class JobOfferVoter extends Voter
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    protected function supports(string $attribute, $recruiter): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['JOB_OFFER_EDIT', 'JOB_OFFER_VIEW','JOB_OFFER_MODIFY','JOB_OFFER_VIEW_BY_REC']);
            
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
            case 'JOB_OFFER_EDIT':
                // logic to determine if the user can EDIT
                // return true or false

                return $this->canEdit($subject, $user);
                break;
            
                case 'JOB_OFFER_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                return $this->canView($subject, $user);
                break;

                case 'JOB_OFFER_VIEW_BY_REC':
                    // logic to determine if the user can VIEW
                    // return true or false
                    return $this->canViewRecruiter($subject, $user);
                    break;


                case 'JOB_OFFER_MODIFY':
                    // logic to determine if the user can VIEW
                    // return true or false
                    return $this->canModify($subject, $user);
                    break;
                
        }

        return false;
    }

    


    private function canDelete(JobOffer $jobOffer, User $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }


    private function canView(JobOffer $jobOffer, User $user)
    {
        return $jobOffer->getIsValidated();
         
    }

    private function canViewRecruiter(JobOffer $jobOffer, User $user)
    {
        return $jobOffer->getIsValidated();
         
    }
    



    private function canEdit(Recruiter $recruiter, User $user)
    {
   
        if ($user->getCategory()->getCategory()=="Admin") {
            return true;
        }
        if( $user === $recruiter->getUser()) {
            
            return true;
        }
        return false;
    }
    
    private function canModify(JobOffer $jobOffer, User $user)
    {
        if ($user->getCategory()->getCategory()=="Admin") {
            return true;
        }
        
        return !$jobOffer->getIsValidated();
         
    }


}
