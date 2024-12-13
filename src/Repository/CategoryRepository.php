<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findBySearchInTitle(string $search): array
    {
        $qb = $this->createQueryBuilder('category');
        $query = $qb->select('category')
            ->where('category.title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery();
        return $query->getResult();
    }
}
