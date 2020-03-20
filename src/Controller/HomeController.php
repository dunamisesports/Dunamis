<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param ArticleRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ArticleRepository $repository)
    {
        $articles = $repository->findLastArticle();
        return $this->render('home/home.html.twig', [
            'articles'      => $articles,
            'current_menu'          => 'home',
        ]);
    }
}
