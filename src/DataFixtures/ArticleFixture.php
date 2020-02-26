<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixture extends Fixture
{
    public  function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for($i = 0; $i < 150; $i++)
        {
            $article = new Article();
            $article
                ->setTitle($faker->words(10, true))
                ->setContente($faker->sentences(10, true))
                ->setPublish(false);
            $manager->persist($article);
        }
        $manager->flush();
    }
}