<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Form\MangaType;
use App\Repository\MangaRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/manga')]
#[IsGranted('ROLE_USER')]
final class MangaController extends AbstractController
{
    #[Route(name: 'app_manga_index', methods: ['GET'])]
    public function index(
        Request $request,
        MangaRepository $mangaRepository,
        CategoryRepository $categoryRepository,
        TagsRepository $tagsRepository
    ): Response
    {
        // Récupérer les paramètres de recherche et filtrage
        $search = $request->query->get('search');
        $categoryId = $request->query->get('category');
        $tagIds = $request->query->all('tags');
        
        // Récupérer le numéro de page actuel (par défaut: 1)
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 20; // Nombre de mangas par page
        
        // Récupération des mangas selon les filtres avec pagination
        if (!$search && !$categoryId && empty($tagIds)) {
            $pagination = $mangaRepository->findPaginated($page, $limit);
        } else {
            $pagination = $mangaRepository->findByFilterPaginated($search, (int) $categoryId, $tagIds, $page, $limit);
        }
        
        // Récupération de toutes les catégories pour le filtre
        $categories = $categoryRepository->findAll();
        
        // Récupération de tous les tags pour le filtre
        $tags = $tagsRepository->findAll();
        
        return $this->render('manga/index.html.twig', [
            'mangas' => $pagination['items'],
            'categories' => $categories,
            'tags' => $tags,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $pagination['total_pages'],
                'total_items' => $pagination['total_items'],
                'route' => 'app_manga_index',
                'route_params' => array_filter([
                    'search' => $search,
                    'category' => $categoryId,
                    'tags' => !empty($tagIds) ? $tagIds : null
                ])
            ],
        ]);
    }

    #[Route('/new', name: 'app_manga_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $manga = new Manga();
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($manga);
            $entityManager->flush();

            $this->addFlash('succes', 'Manga sauvegardé !');
            return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manga/new.html.twig', [
            'manga' => $manga,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_manga_show', methods: ['GET'])]
    public function show(Manga $manga): Response
    {
        return $this->render('manga/show.html.twig', [
            'manga' => $manga,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_manga_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MangaType::class, $manga);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manga/edit.html.twig', [
            'manga' => $manga,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_manga_delete', methods: ['POST'])]
    public function delete(Request $request, Manga $manga, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$manga->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($manga);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_manga_index', [], Response::HTTP_SEE_OTHER);
    }
}