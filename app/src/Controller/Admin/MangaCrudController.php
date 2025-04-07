<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Manga;
use App\Entity\Tags;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\MakerBundle\Doctrine\RelationOneToMany;

class MangaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Manga::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            MoneyField::new('price')->setCurrency('EUR'),
            AssociationField::new('category')
                ->setCrudController(CategoryCrudController::class),
            AssociationField::new('tags')
                ->setFormTypeOption('by_reference', false)
                ->setCrudController(TagsCrudController::class)
                ->setFormTypeOptions([
                    'multiple' => true,
                    'choice_label' => function(Tags $tag) {
                        return ucfirst($tag->getName());
                    }
                ]),
            DateTimeField::new('createdAt')->hideOnForm(),
            DateTimeField::new('updatedAt')->hideOnForm(),
        ];
    }
}