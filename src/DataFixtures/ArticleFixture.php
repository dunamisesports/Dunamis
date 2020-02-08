<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixture extends Fixture
{
    public  function load(ObjectManager $manager)
    {
        for($i = 0; $i < 20; $i++)
        {
            $article = new Article();
            $article
                ->setTitle('La premiere article')
                ->setContente("As I commented in the introduction of the article, DoctrineFixturesBundle allows the insertion of test data into our database in order to perform tests or other actions")
                ->setPublish(false);
            $manager->persist($article);
        }
        $manager->flush();
    }
}