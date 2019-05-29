<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Article;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Repository\ArticleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use  Faker;
use App\Service\Slugify;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
    public function load(ObjectManager $manager)
    {
        $faker  =  Faker\Factory::create('fr_FR');
        $slugify = new  Slugify();
        for ($i = 0; $i<=50; $i++) {
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->title));
            $article->setContent(mb_strtolower($faker->text));
            $article->setCategory($this->getReference('category_'.random_int(0, 5)));
            $article->setSlug($slugify->generate($article->getTitle()));
            $manager->persist($article);
        }
        $manager->flush();
    }
}