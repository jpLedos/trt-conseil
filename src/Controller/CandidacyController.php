<?php

namespace App\Controller;

use App\Entity\Candidacy;
use App\Entity\JobOffer;
use App\Form\CandidacyType;
use App\Repository\CandidacyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\CandidateRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;




#[Route('/candidacy')]
class CandidacyController extends AbstractController
{
    #[Route('/', name: 'candidacy_index', methods: ['GET'])]
    public function index(CandidacyRepository $candidacyRepository): Response
    {
        return $this->render('candidacy/index.html.twig', [
            'candidacies' => $candidacyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'candidacy_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidacy = new Candidacy();

        $form = $this->createForm(CandidacyType::class, $candidacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($candidacy);
            $entityManager->flush();

            return $this->redirectToRoute('candidacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidacy/new.html.twig', [
            'candidacy' => $candidacy,
            'form' => $form,
        ]);
    }

    #[Route('/new/{jobOffer}', name: 'candidacy_new_job_by_id', methods: ['GET', 'POST'])]
    public function newByJobOffer(Request $request, 
                                    JobOffer $jobOffer,
                                    EntityManagerInterface $entityManager,
                                    UserInterface $user, 
                                    CandidateRepository $canditateRepo ): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_CANDIDATE');
        $candidate = $canditateRepo->findOneByUser($user);
        $candidacy = new Candidacy();
        $candidacy->setIsValidated(false);
        $candidacy->setJobOffer($jobOffer);
        $candidacy->setCandidate($candidate);

        $entityManager->persist($candidacy);
        $entityManager->flush();

        return $this->redirectToRoute('candidate_job_offer_index');



    }


    #[Route('/{id}', name: 'candidacy_show', methods: ['GET'])]
    public function show(Candidacy $candidacy): Response
    {
        return $this->render('candidacy/show.html.twig', [
            'candidacy' => $candidacy,
        ]);
    }

    #[Route('/{id}/edit', name: 'candidacy_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidacy $candidacy, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(CandidacyType::class, $candidacy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('candidacy_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidacy/edit.html.twig', [
            'candidacy' => $candidacy,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'candidacy_delete', methods: ['POST'])]
    public function delete(Request $request, Candidacy $candidacy, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidacy->getId(), $request->request->get('_token'))) {
            $entityManager->remove($candidacy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidacy_index', [], Response::HTTP_SEE_OTHER);
    }

    

    #[Route('/{id}/toogle', name: 'candidacy_toogle', methods: ['GET'])]
    public function toogle(Candidacy $candidacy, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $candidacy->setIsValidated(!$candidacy->getIsValidated());
        $entityManager->persist($candidacy);
        $entityManager->flush();

        if($candidacy->getIsValidated()) {
            
            $to =$candidacy->getCandidate()->getUser()->getEmail();
            $email = (new TemplatedEmail())
            ->from($candidacy->getCandidate()->getUser()->getEmail())
            ->to( new Address('jpledos@gmail.com') )
            ->replyTo()
            ->subject('Candidature pour l\'annonce :'.$candidacy->getJobOffer()->getJobTitle())
            ->attach($candidacy->getCandidate()->getCv())
            ->htmlTemplate('canditature.html.twig')
            ->context([
                'message' => 'candidature de '.$candidacy->getCandidate()->getFullname(),
            ]);

            $mailer->send($email);
        }
 
        return $this->redirectToRoute('candidacy_show', ['id'=>$candidacy->getId()], Response::HTTP_SEE_OTHER);
    }

}
