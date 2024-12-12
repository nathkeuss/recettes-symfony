<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findBySearchInTitle(string $search): array
    {
        $qb = $this->createQueryBuilder('recipe');
                    // sélectionne l'entité Recipe
        $query = $qb->select('recipe')
                    //ajoute une condition pour les titres contenant le terme recherché
                    ->where('recipe.title LIKE :search')
                    // définit le paramètre 'search' avec des jokers '%' autour de la valeur recherchée
                    // pour permettre une recherche partielle dans le titre
                    ->setParameter('search', '%' . $search . '%')
                    // génère la requête finale
                    ->getQuery();

        // exécute la requête et retourne les résultats
        // 'getResult()' renvoie un tableau des entités 'recipe' qui correspondent à la recherche
        return $query->getResult();
    }
}
