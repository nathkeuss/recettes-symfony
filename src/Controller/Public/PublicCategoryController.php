<?php

namespace App\Controller\Public;


use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PublicCategoryController extends AbstractController
{

    #[Route('/categories', 'list_categories', methods: ['GET'])]
    public function listCategories(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('public/category/list_category.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/categories/{id}', 'show_category', requirements: ['id' => '\d+'],methods: ['GET'])]
    public function showCategory(int $id, CategoryRepository $categoryRepository, RecipeRepository $recipeRepository)
    {
        $publishedRecipes = $recipeRepository->findBy(['isPublished' => true, 'category' => $categoryRepository->find($id)]);
        $category = $categoryRepository->find($id);

        return $this->render('public/category/show_category.html.twig', [
            'category' => $category,
            'publishedRecipes' => $publishedRecipes
        ]);
    }


    #[Route('/categories/search', 'search_categories', methods: ['GET'])]
    public function searchCategories(Request $request, CategoryRepository $categoryRepository)
    {

        $search = $request->query->get('search');

        $categories = $categoryRepository->findBySearchInTitle($search);

        return $this->render('public/category/search_category.html.twig', [
            'search' => $search,
            'categories' => $categories
        ]);
    }

}