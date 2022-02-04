<?php

namespace App\Controller;

use App\Entity\Consultant;
use App\Entity\User;
use App\Form\ConsultantType;
use App\Repository\ConsultantRepository;
use App\Repository\UserCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route('/consultant')]
class ConsultantController extends AbstractController
{
    #[Route('/', name: 'consultant_index', methods: ['GET'])]
    public function index(ConsultantRepository $consultantRepository): Response
    {
        return $this->render('consultant/index.html.twig', [
            'consultants' => $consultantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'consultant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher ,UserCategoryRepository $UCatRepo): Response
    {
        $user = new User();
        $consultant = new Consultant();
        
        $form = $this->createForm(ConsultantType::class, $consultant);
        $form ->add('email', EmailType::class, [
                    'mapped' => false,
                ] )
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrez un mot de passe',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Le mot de passe doit avoir {{ limit }} charactères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $category = $UCatRepo->find(3);
            $user->setCategory($category);
            $user->setIsActived(true);
            $user->setEmail($form->get('email')->getData());
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            $entityManager->persist($user); 
            $consultant->setUser($user);
            $entityManager->persist($consultant);
            $entityManager->flush();

            return $this->redirectToRoute('consultant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('consultant/new.html.twig', [
            'consultant' => $consultant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'consultant_show', methods: ['GET'])]
    public function show(Consultant $consultant): Response
    {
        return $this->render('consultant/show.html.twig', [
            'consultant' => $consultant,
        ]);
    }

    #[Route('/{id}/edit', name: 'consultant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consultant $consultant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsultantType::class, $consultant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('consultant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('consultant/edit.html.twig', [
            'consultant' => $consultant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'consultant_delete', methods: ['POST'])]
    public function delete(Request $request, Consultant $consultant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$consultant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($consultant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('consultant_index', [], Response::HTTP_SEE_OTHER);
    }
}
