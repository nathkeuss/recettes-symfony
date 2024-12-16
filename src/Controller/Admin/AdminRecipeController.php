<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\AdminRecipeType;
use App\Repository\RecipeRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRecipeController extends AbstractController
{

    #[Route('admin/recipe/create', 'admin_create_recipe', methods: ['GET', 'POST'])]
    public function createRecipe(UniqueFilenameGenerator $uniqueFilenameGenerator,Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        //nouvelle instance de l'entité Recipe, qui sera utilisée pour construire le formulaire
        $recipe = new Recipe();

        //création du formulaire basé sur la classe AdminRecipeType
        //elle définit la structure et les champs du formulaire
        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);

        //traite les données de la requête et les associe au formulaire
        $adminRecipeForm->handleRequest($request);

        //si le formulaire a été soumis
        if ($adminRecipeForm->isSubmitted() && $adminRecipeForm->isValid()) {

            $recipeImage = $adminRecipeForm->get('image')->getData();

            if ($recipeImage) {

                //récupère le nom original de l'image (exemple : poulet.png)
                $imageOriginalName = $recipeImage->getClientOriginalName();
                //récupère l'extension (png, jpeg etc) de l'image
                $imageExtension = $recipeImage->guessExtension();
                //utilise une fonction de ma class UniqueFilenameGenerator, que j'ai instancié dans les
                //paramètres de la fonction updateRecipe, la fonction de la class instanciée
                //prend en premier paramètre :
                //le nom de l'image, qui lui donne une id unique, hash le nom, lui donne le timestamp actuel
                //et en second paramètre ajoute l'extension de l'image.
                $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

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
    public function updateRecipe(int $id, UniqueFilenameGenerator $uniqueFilenameGenerator, Request $request, EntityManagerInterface $entityManager, RecipeRepository $recipeRepository, ParameterBagInterface $parameterBag)
    {
        //récupère la recette qui correspond à l'id dans l'url
        $recipe = $recipeRepository->find($id);

        //crée un formulaire prérempli avec les données de la recette
        $adminRecipeForm = $this->createForm(AdminRecipeType::class, $recipe);
        //lie les données de la requête au formulaire
        $adminRecipeForm->handleRequest($request);

        //vérifie si le formulaire est soumis
        if ($adminRecipeForm->isSubmitted() && $adminRecipeForm->isValid()) {

            //récupère l'image du formulaire
            $recipeImage = $adminRecipeForm->get('image')->getData();
            {
                if ($recipeImage) {
                    //récupère le nom original de l'image (exemple : poulet.png)
                    $imageOriginalName = $recipeImage->getClientOriginalName();
                    //récupère l'extension (png, jpeg etc) de l'image
                    $imageExtension = $recipeImage->guessExtension();
                    //utilise une fonction de ma class UniqueFilenameGenerator, que j'ai instancié dans les
                    //paramètres de la fonction updateRecipe, la fonction de la class instanciée
                    //prend en premier paramètre :
                    //le nom de l'image, qui lui donne une id unique, hash le nom, lui donne le timestamp actuel
                    //et en second paramètre ajoute l'extension de l'image.
                    $imageNewFilename = $uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

                    //récupère le répertoire racine du projet
                    $rootDir = $parameterBag->get('kernel.project_dir');
                    //définit le dossier de stockage
                    $imgDir = $rootDir . '/public/assets/img';
                    //déplace l'image
                    $recipeImage->move($imgDir, $imageNewFilename);
                    //définit le nom de l'image dans l'entité
                    $recipe->setImage($imageNewFilename);
                }
            }
            //prépare la mise à jour
            $entityManager->persist($recipe);
            //sauvegarde les changements en bdd
            $entityManager->flush();
            //message de confirmation
            return $this->redirectToRoute('admin_list_recipes');
            //redirige vers la liste
            $this->addFlash('success', 'Recette modifiée');
        }

        $adminRecipeFormView = $adminRecipeForm->createView();

        return $this->render('admin/recipe/update_recipe.html.twig', [
            'adminRecipeFormView' => $adminRecipeFormView,
        ]);
    }

    #[Route('/admin/recipe/{id}/delete', 'admin_delete_recipe', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function deleteRecipe(int $id, RecipeRepository $recipeRepository, EntityManagerInterface $entityManager)
    {
        //récupère la recette qui correspond à l'id dans l'url
        $recipe = $recipeRepository->find($id);

        //prépare la suppression
        $entityManager->remove($recipe);
        //supprime en bdd
        $entityManager->flush();
        //ajout message confirmation
        $this->addFlash('succes', 'Recette supprimée');
        //redirige vers la liste des recettes
        return $this->redirectToRoute('admin_list_recipes');

    }
}