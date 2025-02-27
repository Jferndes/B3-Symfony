<?php

namespace App\DataFixtures;

use App\Entity\Manga;
use Faker\Factory;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class MangaFixtures extends Fixture
{
    function __construct(private CategoryRepository $categoryRepository)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
         
        for($i = 0; $i < 20; $i++){
            $manga = new Manga();
            $manga->setPrice(mt_rand(3, 10));
            $manga->setTitle($faker->name());
            $manga->setCategory($this->categoryRepository->find(mt_rand(7,12)));
            $manager->persist($manga);
        }

        $manager->flush();
    }
}
