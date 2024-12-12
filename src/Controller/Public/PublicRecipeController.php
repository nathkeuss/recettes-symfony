<?php

namespace App\Controller\Public;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicRecipeController extends AbstractController
{

    #[Route('/recipes', 'list_recipes', methods: ['GET'])]
    public function listPublishedRecipes(RecipeRepository $recipeRepository)
    {
        $publishedRecipes = $recipeRepository->findBy(['isPublished' => true]);
        // récupère les recettes publiées depuis la base de données
        return $this->render('public/recipe/list_recipe.html.twig', [
            'publishedRecipes' => $publishedRecipes
        ]);
    }


    #[Route('/recipes/{id}', 'show_recipe', requirements: ['id' => '\d+'],methods: ['GET'])]
    public function showRecipe(int $id, RecipeRepository $recipeRepository)
    {
        // recherche la recette par son ID
        $recipe = $recipeRepository->find($id);

        // vérifie si la recette existe et est publiée
        if (!$recipe || !$recipe->isPublished()) {
            $notFound = new Response('Recette non trouvée', 404);
            // retourne une réponse 404 si la recette est introuvable ou non publiée
            return $notFound;
        }

        return $this->render('public/recipe/show_recipe.html.twig', [
            'recipe' => $recipe
        ]);
    }


    #[Route('/recipes/search', 'search_recipes', methods: ['GET'])]
    public function searchRecipes(Request $request, RecipeRepository $recipeRepository)
    {

        // récupère le paramètre 'search' de la requête GET
        $search = $request->query->get('search');

        // utilise le terme 'search' pour trouver des recettes via la requête SQL définie dans le Repo
        $recipes = $recipeRepository->findBySearchInTitle($search);

        return $this->render('public/recipe/search_recipe.html.twig', [
            'search' => $search, //retourne notre recherche (pas le résultat)
            'recipes' => $recipes // retourne le résultat de notre recherche
        ]);
    }

}