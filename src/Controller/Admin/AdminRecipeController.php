<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{

    #[Route('admin/recipe/create', 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function createRecipe(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        //nouvelle instance de l'entité Recipe, qui sera utilisée pour construire le formulaire
        $recipe = new Recipe();

        //création du formulaire basé sur la classe AdminRecipeType
        //elle définit la structure et les champs du formulaire
        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        //traite les données de la requête et les associe au formulaire
        $adminRecipeForm->handleRequest($request);

        //si le formulaire a été soumis
        if ($adminRecipeForm->isSubmitted()) {

            $recipeImage = $adminRecipeForm->get('image')->getData();

            if ($recipeImage) {
                $imageNewFilename = uniqid() . '.' . $recipeImage->guessExtension();

                $rootDir = $parameterBag->get('kernel.project_dir');

                $imgDir = $rootDir . '/public/assets/img';

                $recipeImage->move($imgDir, $imageNewFilename);

                $recipe->setImage($imageNewFilename);
            }

            //prépare l'insertion dans la bdd
            $entityManager->persist($recipe);
            // enregistre les changements dans la bdd
            $entityManager->flush();

            //message pour dire que ça a marché
            $this->addFlash('success', 'Recette créée!');
        }

        //générer la vue du formulaire dans la template twig
        $adminRecipeFormView = $adminRecipeForm->createView();

        //rend la vue create_recipe et reçoit la variable
        //adminRecipeFormView qui contient la vue du formulaire
        return $this->render('admin/recipe/create_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
        ]);
    }

    #[Route('admin/recipes/list', 'admin_list_recipes', methods: ['GET'])]
    public function listRecipes(RecipeRepository $recipeRepository)
    {
        //récupère toutes les recettes
        $recipes = $recipeRepository->findAll();

        //rend la vue avec la liste des recettes
        return $this->render('admin/recipe/list_recipes.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/admin/recipe/{id}/update', 'admin_update_recipe', requirements: ['id' => '\d+'] ,methods: ['POST', 'GET'])]
    public function updateRecipe(int $id, Request $request, EntityManagerInterface $entityManager, RecipeRepository $recipeRepository)
    {
        $recipe = $recipeRepository->find($id);

        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);
        $adminRecipeForm->handleRequest($request);

        if ($adminRecipeForm->isSubmitted()) {
            $entityManager->persist($recipe);
            $entityManager->flush();
            return $this->redirectToRoute('admin_list_recipes');
        }

        $adminRecipeFormView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/update_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
        ]);
    }

    #[Route('/admin/recipe/{id}/delete', 'admin_delete_recipe', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteRecipe(int $id, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager)
    {

        $recipe = $recipeRepository->find($id);

        $entityManager->remove($recipe);
        $entityManager->flush();
        $this->addFlash('succes', 'Recette supprimée');

        return $this->redirectToRoute('admin_list_recipes');

    }
}