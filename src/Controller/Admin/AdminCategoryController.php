<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\AdminCategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{

    #[Route('admin/category/create', 'admin_create_category', methods: ['GET', 'POST'])]
    public function createCategory(Request $request, EntityManagerInterface $entityManager)
    {

        $category = new Category();

        $adminCategoryForm = $this->createForm(AdminCategoryType::class, $category);

        $adminCategoryForm->handleRequest($request);

        if ($adminCategoryForm->isSubmitted() && $adminCategoryForm->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie créée!');
        }

        $adminCategoryFormView = $adminCategoryForm->createView();

        return $this->render('admin/category/create_category.html.twig', [
            'adminCategoryFormView' => $adminCategoryFormView,
        ]);
    }

    #[Route('admin/categories/list', 'admin_list_categories', methods: ['GET'])]
    public function listCategory(CategoryRepository $categoryRepository)
    {

        $categories = $categoryRepository->findAll();

        return $this->render('admin/category/list_categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/{id}/update', 'admin_update_category', requirements: ['id' => '\d+'] ,methods: ['POST', 'GET'])]
    public function updateCategory(int $id, Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {

        $category = $categoryRepository->find($id);

        $adminCategoryForm = $this->createForm(AdminCategoryType::class, $category);
        $adminCategoryForm->handleRequest($request);

        if ($adminCategoryForm->isSubmitted() && $adminCategoryForm->isValid()) {

            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('admin_list_categories');
            $this->addFlash('success', 'Catégorie modifiée');
        }

        $adminCategoryFormView = $adminCategoryForm->createView();

        return $this->render('admin/category/update_category.html.twig', [
            'adminCategoryFormView' => $adminCategoryFormView,
        ]);
    }

    #[Route('/admin/category/{id}/delete', 'admin_delete_category', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteCategory(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {

        $category = $categoryRepository->find($id);

        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash('succes', 'Catégorie supprimée');
        return $this->redirectToRoute('admin_list_categories');

    }

}