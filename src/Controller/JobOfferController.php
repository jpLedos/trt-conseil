<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\Consultant;
use App\Form\JobOfferType;
use App\Repository\JobOfferRepository;
use App\Repository\CandidateRepository;
use App\Repository\CandidacyRepository;
use App\Entity\Recruiter;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


#[Route('/job/offer')]
class JobOfferController extends AbstractController
{
    #[Route('/', name: 'job_offer_index', methods: ['GET'])]
    public function index(JobOfferRepository $jobOfferRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');
        return $this->render('job_offer/index.html.twig', [
            'job_offers' => $jobOfferRepository->findAll(),
        ]);
    }

    #[Route('/candidate', name: 'candidate_job_offer_index', methods: ['GET'])]
    public function candidateIndex(JobOfferRepository $jobOfferRepository, CandidateRepository $candidateRepository, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');
        return $this->render('job_offer/candidate_index.html.twig', [
            'job_offers' => $jobOfferRepository->findByValidated(),
            'candidate' => $candidateRepository->findOneByUser($user)
        ]);
    }

    #[Route('/recruiter', name: 'recruiter_job_offer_index', methods: ['GET'])]
    public function recruiterIndex(JobOfferRepository $jobOfferRepository, RecruiterRepository $recruiterRepository, UserInterface $user,CandidacyRepository $candidacyRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');                
        $recruiter = $recruiterRepository->findOneByUser($user);
        return $this->render('job_offer/recruiter_index.html.twig', [
            'job_offers' => $jobOfferRepository->findByRecruiter($recruiter),
            'candidacies' => $candidacyRepository->findAll()
        ]);
    }


    
    #[Route('/new', name: 'job_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user, RecruiterRepository $recruiterRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_RECRUITER');
        $jobOffer = new JobOffer();
        $jobOffer->setIsValidated(false);
        $jobOffer->setRecruiter($recruiterRepo->findOneByUser($user));
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobOffer);
            $entityManager->flush();

            return $this->redirectToRoute('recruiter_job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_offer/new.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'job_offer_show', methods: ['GET'])]
    public function show(JobOffer $jobOffer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');
        return $this->render('job_offer/show.html.twig', [
            'job_offer' => $jobOffer,
        ]);
    }

    #[Route('/candidate/{id}', name: 'candidate_job_offer_show', methods: ['GET'])]
    public function candidateShow(JobOffer $jobOffer, CandidateRepository $candidateRepository, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted("JOB_OFFER_VIEW", $jobOffer,'Vous n\'avez accès qu\'au offre valide');
        return $this->render('job_offer/candidate_show.html.twig', [
            'job_offer' => $jobOffer,
            'candidate' => $candidateRepository->findOneByUser($user),
        ]);
    }

    #[Route('/{id}/recruiter/', name: 'recruiter_job_offer_show', methods: ['GET'])]
    public function recruiterShow(JobOffer $jobOffer, RecruiterRepository $recruiterRepository, UserInterface $user): Response
    {
        $recruiter=$jobOffer->getRecruiter();
        $this->denyAccessUnlessGranted("JOB_OFFER_EDIT", $recruiter);
 
        return $this->render('job_offer/recruiter_show.html.twig', [
            'job_offer' => $jobOffer,
            'recruiter' => $recruiterRepository->findOneByUser($user),
        ]);
    }



    #[Route('/{id}/edit', name: 'job_offer_edit', methods: ['GET', 'POST'])]
    public function edit(JobOffer $jobOffer, Request $request , EntityManagerInterface $entityManager): Response
    {
   
        $recruiter=$jobOffer->getRecruiter();
        $this->denyAccessUnlessGranted("JOB_OFFER_EDIT", $recruiter);
        $this->denyAccessUnlessGranted("JOB_OFFER_MODIFY", $jobOffer);
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_offer/edit.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form,
        ]);
    }


    
    #[Route('/{id}/recruiter/edit', name: 'recruiter_job_offer_edit', methods: ['GET', 'POST'])]
    public function recruiterEdit(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager, RecruiterRepository $recruiterRepo, UserInterface $user): Response
    {
        $recruiter = $jobOffer->getRecruiter();

        $this->denyAccessUnlessGranted("JOB_OFFER_EDIT", $recruiter);
        $this->denyAccessUnlessGranted("JOB_OFFER_MODIFY", $jobOffer);
        $recruiter=$jobOffer->getRecruiter();
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('recruiter_job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('job_offer/edit.html.twig', [
            'job_offer' => $jobOffer,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'job_offer_delete', methods: ['POST'])]
    public function delete(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$jobOffer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($jobOffer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_offer_index', [], Response::HTTP_SEE_OTHER);
    }

    
    #[Route('/{id}/toogle', name: 'job_offer_toogle', methods: ['GET'])]
    public function toogle(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');
        $jobOffer->setIsValidated(!$jobOffer->getIsValidated());

        $entityManager->persist($jobOffer);
        $entityManager->flush();
     
        return $this->redirectToRoute('job_offer_show', ['id'=>$jobOffer->getId()], Response::HTTP_SEE_OTHER);
    }

}
