<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\CandidateRepository;
use App\Repository\RecruiterRepository;
use App\Repository\ConsultantRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;


class LandingController extends AbstractController
{
    #[Route('/landing', name: 'landing')]
    public function index(UserInterface $user, 
        CandidateRepository $candidateRepository, 
        RecruiterRepository $recruiterRepository,
        ConsultantRepository $consultantRepository ): RedirectResponse
    {
        
        Switch($user->getCategory()->getId()) {
            case 1:
                $candidate = $candidateRepository->findOneByUser($user);
                if($candidate){
                    //on va editer le profile
                    return $this->redirectToRoute('candidate_edit',['id'=>$candidate->getId()]);
                } else {
                    // on va creer le profil
                    return $this->redirectToRoute('candidate_new');
                }
                break;

            case 2:
                $recruiter = $recruiterRepository->findOneByUser($user);
                if($recruiter){
                    //on va editer le profile
                    return $this->redirectToRoute('recruiter_edit',['id'=>$recruiter->getId()]);
                } else {
                    // on va creer le profil
                    return $this->redirectToRoute('recruiter_new');
                }
                break; 

            case 3:
                
                $consultant = $consultantRepository->findOneByUser($user);
                if($consultant){
                    //on va editer le profile
                    return $this->redirectToRoute('consultant_edit',['id'=>$consultant->getId()]);
                } else {
                    // on va creer le profil
                    return $this->redirectToRoute('consultant_new');
                }
                break;
            
                case 4:
                echo('bienvenue admin');
                break;
            default:
            return $this->redirectToRoute('index');
                break;
        }

    }
}
