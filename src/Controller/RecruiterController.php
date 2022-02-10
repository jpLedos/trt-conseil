<?php

namespace App\Controller;

use App\Entity\Recruiter;
use App\Form\RecruiterType;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/recruiter')]
class RecruiterController extends AbstractController
{
    #[Route('/', name: 'recruiter_index', methods: ['GET'])]
    public function index(RecruiterRepository $recruiterRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_CONSULTANT", null, "L'acces à cette page est reservé aux consultants");
        $this->denyAccessUnlessGranted("ROLE_CONSULTANT", null, "L'acces à cette page est reservé aux consultants");
        return $this->render('recruiter/index.html.twig', [
            'recruiters' => $recruiterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'recruiter_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        
        $recruiter = new Recruiter();
        $recruiter->setUser($user);

        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recruiter);
            $entityManager->flush();

            return $this->redirectToRoute('recruiter_show', ['id'=>$recruiter->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recruiter/new.html.twig', [
            'recruiter' => $recruiter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'recruiter_show', methods: ['GET'])]
    public function show(Recruiter $recruiter): Response
    {
        
        $this->denyAccessUnlessGranted("RECRUITER_EDIT", $recruiter);
        return $this->render('recruiter/show.html.twig', [
            'recruiter' => $recruiter,
        ]);
    }

    #[Route('/consultant/{id}', name: 'consultant_recruiter_show', methods: ['GET'])]
    public function consultantShow(Recruiter $recruiter): Response
    {
        
        $this->denyAccessUnlessGranted("ROLE_CONSULTANT");
        return $this->render('recruiter/consultant_show.html.twig', [
            'recruiter' => $recruiter,
        ]);
    }



    #[Route('/{id}/edit', name: 'recruiter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        
        $this->denyAccessUnlessGranted("RECRUITER_EDIT", $recruiter);
        $form = $this->createForm(RecruiterType::class, $recruiter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('recruiter_show', ['id'=>$recruiter->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recruiter/edit.html.twig', [
            'recruiter' => $recruiter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'recruiter_delete', methods: ['POST'])]
    public function delete(Request $request, Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recruiter->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recruiter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('recruiter_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toogle', name: 'recruiter_toogle', methods: ['GET'])]
    public function toogle(Request $request, Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        $user = $recruiter->getUser();
        $user->setIsActived(!$user->GetIsActived());
    
        $entityManager->persist($user);
        $entityManager->flush();
     
        return $this->redirectToRoute('consultant_recruiter_show', ['id'=>$recruiter->getId()], Response::HTTP_SEE_OTHER);
    }
}
