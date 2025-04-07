<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;

class TagsCrudController extends AbstractCrudController
{
    private TagsRepository $tagsRepository;

    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    public static function getEntityFqcn(): string
    {
        return Tags::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
                ->setFormTypeOption('attr', [
                    'placeholder' => 'Nom du tag (sera enregistré en minuscules)'
                ])
                ->formatValue(function ($value, $entity) {
                    // Affiche le nom du tag avec une première lettre majuscule dans l'interface
                    return $value ? ucfirst($value) : '';
                }),
        ];
    }

    /**
     * Vérifie si un tag existe déjà avant de le persister
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Tags $entityInstance */
        $tagName = $entityInstance->getName(); // Le nom sera déjà en minuscules grâce au setter
        
        // Vérifier si un tag avec le même nom existe déjà
        $existingTag = $this->tagsRepository->findOneByName($tagName);
        
        if ($existingTag !== null) {
            // Si le tag existe déjà, on affiche un message et on ne persiste pas
            $this->addFlash('warning', sprintf('Le tag "%s" existe déjà en base de données.', $tagName));
            return;
        }
        
        // Si le tag n'existe pas, on le persiste normalement
        parent::persistEntity($entityManager, $entityInstance);
        $this->addFlash('success', sprintf('Le tag "%s" a été créé avec succès.', $tagName));
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gestion des Tags')
            ->setPageTitle('new', 'Créer un nouveau Tag')
            ->setPageTitle('edit', 'Modifier un Tag')
            ->setPageTitle('detail', 'Détails du Tag');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Ajouter un Tag');
            });
    }
}