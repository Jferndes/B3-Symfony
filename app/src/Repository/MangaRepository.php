<?php

namespace App\Repository;

use App\Entity\Manga;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
     * Trouve tous les mangas avec pagination
     * 
     * @param int $page La page actuelle
     * @param int $limit Nombre d'éléments par page
     * @return array Tableau contenant les éléments paginés et infos de pagination
     */
    public function findPaginated(int $page = 1, int $limit = 20): array
    {
        $query = $this->createQueryBuilder('m')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $this->paginate($query, $page, $limit);
    }

    /**
     * Recherche des mangas par titre, catégorie et tags avec pagination
     * 
     * @param string|null $search Texte recherché
     * @param int|null $categoryId ID de la catégorie
     * @param array|null $tagIds IDs des tags sélectionnés
     * @param int $page La page actuelle
     * @param int $limit Nombre d'éléments par page
     * @return array Tableau contenant les éléments paginés et infos de pagination
     */
    public function findByFilterPaginated(?string $search = null, ?int $categoryId = null, ?array $tagIds = null, int $page = 1, int $limit = 20): array
    {
        // Approche en deux étapes pour éviter les problèmes de GROUP BY
        
        // 1. Si des tags sont sélectionnés, trouvons d'abord les IDs des mangas qui correspondent
        $mangaIds = [];
        if ($tagIds && count($tagIds) > 0) {
            $tagCount = count($tagIds);
            
            $subQb = $this->createQueryBuilder('sub_m')
                ->select('sub_m.id')
                ->join('sub_m.tags', 'sub_t')
                ->where('sub_t.id IN (:tagIds)')
                ->setParameter('tagIds', $tagIds)
                ->groupBy('sub_m.id')
                ->having('COUNT(DISTINCT sub_t.id) = :tagCount')
                ->setParameter('tagCount', $tagCount);
            
            // Exécuter cette requête pour obtenir les IDs
            $result = $subQb->getQuery()->getScalarResult();
            $mangaIds = array_column($result, 'id');
            
            // Si aucun manga ne correspond aux tags, retourner un tableau vide immédiatement
            if (empty($mangaIds)) {
                return [
                    'items' => [],
                    'total_items' => 0,
                    'total_pages' => 1
                ];
            }
        }
        
        // 2. Maintenant construire la requête principale
        $qb = $this->createQueryBuilder('m');
        
        // Ajouter la condition sur les IDs si nécessaire
        if ($tagIds && count($tagIds) > 0) {
            $qb->andWhere('m.id IN (:mangaIds)')
               ->setParameter('mangaIds', $mangaIds);
        }
        
        // Ajouter les autres filtres
        if ($search) {
            $qb->andWhere('m.title LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }
        
        if ($categoryId) {
            $qb->andWhere('m.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }
            
        $qb->orderBy('m.title', 'ASC');
        $query = $qb->getQuery();
        
        return $this->paginate($query, $page, $limit);
    }
    
    /**
     * Méthode de pagination générique
     */
    private function paginate($dql, int $page = 1, int $limit = 20): array
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
            
        $total = count($paginator);
        
        return [
            'items' => $paginator,
            'total_items' => $total,
            'total_pages' => max(1, ceil($total / $limit))
        ];
    }
}