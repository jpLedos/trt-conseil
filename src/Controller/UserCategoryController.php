<?php

namespace App\Controller;

use App\Entity\UserCategory;
use App\Form\UserCategoryType;
use App\Repository\UserCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/category')]
class UserCategoryController extends AbstractController
{
    #[Route('/', name: 'user_category_index', methods: ['GET'])]
    public function index(UserCategoryRepository $userCategoryRepository): Response
    {
        return $this->render('user_category/index.html.twig', [
            'user_categories' => $userCategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userCategory = new UserCategory();
        $form = $this->createForm(UserCategoryType::class, $userCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userCategory);
            $entityManager->flush();

            return $this->redirectToRoute('user_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_category/new.html.twig', [
            'user_category' => $userCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_category_show', methods: ['GET'])]
    public function show(UserCategory $userCategory): Response
    {
        return $this->render('user_category/show.html.twig', [
            'user_category' => $userCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserCategory $userCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserCategoryType::class, $userCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_category/edit.html.twig', [
            'user_category' => $userCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_category_delete', methods: ['POST'])]
    public function delete(Request $request, UserCategory $userCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($userCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
