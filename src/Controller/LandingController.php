<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\CandidateRepository;
use App\Repository\RecruiterRepository;
use App\Repository\ConsultantRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;


class LandingController extends AbstractController
{
    #[Route('/landing', name: 'landing')]
    public function landing(UserInterface $user, 
        CandidateRepository $candidateRepository, 
        RecruiterRepository $recruiterRepository,
        ConsultantRepository $consultantRepository ): RedirectResponse
    {
     
        
        Switch($user->getCategory()->getCategory()) {
            case 'Candidat':
                $candidate = $candidateRepository->findOneByUser($user);
                if($candidate){
                    if($user->getIsActived()) {
                        // on va montre les offres
                        return $this->redirectToRoute('candidate_job_offer_index');
                    } else {
                        //on va show le profile
                        return $this->redirectToRoute('candidate_show',['id'=>$candidate->getId()]);
                    }
                } else {
                    // on va creer le profil
                    return $this->redirectToRoute('candidate_new');
                }
            break;

            case 'Recruteur':
                $recruiter = $recruiterRepository->findOneByUser($user);
                if($recruiter){
                    if($user->getIsActived()) {
                        // on va montre les offres
                        return $this->redirectToRoute('recruiter_job_offer_index');
                    } else {
                    //on va editer le profile
                    return $this->redirectToRoute('recruiter_show',['id'=>$recruiter->getId()]);
                    }
                } else {
                    // on va creer le profil
                    return $this->redirectToRoute('recruiter_new');
                }
                break; 

            case 'Consultant':
                $consultant = $consultantRepository->findOneByUser($user);
                if($consultant){
                    //on va editer lister les candidats
                    return $this->redirectToRoute('recruiter_index');
                } else {
                    // on va creer le profil
                    return $this->redirectToRoute('consultant_new');
                }
                break;
            
                case 'Admin':
                    return $this->redirectToRoute('consultant_index');
                break;
            default:
            return $this->redirectToRoute('index');
                break;
        }

    }
}
