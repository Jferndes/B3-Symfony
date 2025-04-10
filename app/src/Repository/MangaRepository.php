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
     * Recherche des mangas par titre, catégorie et tags
     * 
     * @param string|null $search Texte recherché
     * @param int|null $categoryId ID de la catégorie
     * @param array|null $tagIds IDs des tags sélectionnés
     * @return Manga[] Returns liste de mangas
     */
    public function findByFilter(?string $search = null, ?int $categoryId = null, ?array $tagIds = null): array
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
        
        if($tagIds && count($tagIds) > 0) {
            $tagCount = count($tagIds);

            //Merci à Khalid pour son aide sur cette requête
            $qb->join('m.tags', 't')
               ->andWhere('t.id IN (:tagIds)')
               ->setParameter('tagIds', $tagIds)
               ->groupBy('m.id')
               ->having('COUNT(DISTINCT t.id) >= :tagCount')
               ->setParameter('tagCount', $tagCount);
        }
            
        return $qb->getQuery()->getResult();
    }
}