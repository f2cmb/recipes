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

    /**
     * @return int Returns the total duration of all recipes
     */
    public function findTotalDuration(): int
    {
        return (int)$this->createQueryBuilder('r')
            ->select('SUM(r.duration) as totalDuration')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Recipe[] Returns an array of Recipe objects
     */
    public function findWithDurationLowerThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.duration < :duration')
            ->setParameter('duration', $duration)
            ->orderBy('r.createdAt', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
