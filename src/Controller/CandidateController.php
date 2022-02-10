<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/candidate')]
class CandidateController extends AbstractController
{
    #[Route('/', name: 'candidate_index', methods: ['GET'])]
    public function index(CandidateRepository $candidateRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_CONSULTANT", null, "Acces à cette page reservé aux consultants");
        return $this->render('candidate/index.html.twig', [
            'candidates' => $candidateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'candidate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, userInterface $user, SluggerInterface $slugger): Response
    {

        $candidate = new Candidate();
        $candidate->setUser($user);
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            /** @var UploadedFile $cv */
            $cv = $form->get('cv')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($cv) {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cv->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $candidate->setCv($newFilename);
            }

            
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('candidate_show', ['id'=>$candidate->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/new.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'candidate_show', methods: ['GET'])]
    public function show(Candidate $candidate): Response
    {
        
        $this->denyAccessUnlessGranted("CANDIDATE_VIEW", $candidate);
        return $this->render('candidate/show.html.twig', [
            'candidate' => $candidate,
        ]);
    }

    #[Route('/consultant/{id}', name: 'consultant_candidate_show', methods: ['GET'])]
    public function consultantShow(Candidate $candidate): Response
    {
        
        $this->denyAccessUnlessGranted("ROLE_CONSULTANT");
        return $this->render('candidate/consultant_show.html.twig', [
            'candidate' => $candidate,
        ]);
    }


    #[Route('/{id}/edit', name: 'candidate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidate $candidate, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted("CANDIDATE_VIEW", $candidate);
        $this->denyAccessUnlessGranted("CANDIDATE_EDIT", $candidate);
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cv */
            $cv = $form->get('cv')->getData();

            if ($cv) {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cv->move(
                        $this->getParameter('cv_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $candidate->setCv($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('candidate_show', ['id'=>$candidate->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('candidate/edit.html.twig', [
            'candidate' => $candidate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'candidate_delete', methods: ['POST'])]
    public function delete(Request $request, Candidate $candidate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$candidate->getId(), $request->request->get('_token'))) {
            $entityManager->remove($candidate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('candidate_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toogle', name: 'candidate_toogle', methods: ['GET'])]
    public function toogle(Request $request, Candidate $candidate, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CONSULTANT');
        $user = $candidate->getUser();
        $user->setIsActived(!$user->GetIsActived());
        //var_dump($user->getEmail());
        //die;
        $entityManager->persist($user);
        $entityManager->flush();
     
        return $this->redirectToRoute('consultant_candidate_show',  ['id'=>$candidate->getId()], Response::HTTP_SEE_OTHER);
    }



}
