<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manga>
 */
class MangaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manga::class);
    }

    /**
     * Recherche des mangas par titre
     * 
     * @param string $search Terme de recherche
     * @return Manga[] Returns an array of Manga objects
     */
    public function findByTitleSearch(string $search): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.title LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des mangas par titre et catégorie
     * 
     * @param string $search Terme de recherche
     * @param int $categoryId ID de la catégorie
     * @return Manga[] Returns an array of Manga objects
     */
    public function findByTitleAndCategory(string $search, int $categoryId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.title LIKE :search')
            ->andWhere('m.category = :categoryId')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
