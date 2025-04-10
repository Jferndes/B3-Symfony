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
     * Recherche des mangas par titre et catégorie
     * 
     * @param string $search Texte recherché
     * @param int $categoryId ID de la catégorie
     * @return Manga[] Returns liste de mangas
     */
    public function findByFilter(string $search, int $categoryId): array
    {
        $qb = $this->createQueryBuilder('m')
            ->orderBy('m.title', 'ASC');
        
        if($search){
            $qb->andWhere('m.title LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        }
        if($categoryId){
            $qb->andWhere('m.category = :categoryId')
            ->setParameter('categoryId', $categoryId);
        }   
            
        $query = $qb->getQuery();
        return $query->execute();
    }
}
