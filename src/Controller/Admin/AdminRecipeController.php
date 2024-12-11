<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{

    #[Route('admin/recipe/create', 'admin_create_recipe', methods: ['GET, POST'])]
    public function createRecipe(Request $request, EntityManagerInterface $entityManager)
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
            //prépare l'insertion dans la bdd
            $entityManager->persist($recipe);
            // enregistre les changements dans la bdd
            $entityManager->flush();
        }

        //générer la vue du formulaire dans la template twig
        $adminRecipeFormView = $adminRecipeForm->createView();

        //rend la vue create_recipe et reçoit la variable
        //adminRecipeFormView qui contient la vue du formulaire
        return $this->render('admin/recipe/create_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
        ]);
    }
}