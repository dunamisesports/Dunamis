<?php

namespace App\Controller\Admin\Article;

use App\Entity\Article;
use App\Form\AticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminArticleController extends AbstractController
{
    private $repository;
    private $em;

    /**
     * ArticleController constructor.
     * @param $repository
     * @param $em
     */
    public function __construct(ArticleRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin/article", name="admin.article.index")
     */
    public function index()
    {
        $articles = $this->repository->findAll();
        return $this->render('admin/article/index.html.twig',
            [
                'articles'  => $articles,
            ]);
    }

    /**
     * @Route("/admin/article/create", name="admin.article.new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(AticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($article);
            $this->em->flush();
            $this->addFlash('success', 'Article créé avec succès');
            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('admin/article/new.html.twig', [
            'article'   => $article,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name="admin.article.edit", methods="GET|POST")
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function edit(Article $article, Request $request): Response
    {
        $form = $this->createForm(AticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($article->getPublish() === true)
            {
                $article->setPublishDate(new \DateTime());
            }
            $this->em->flush();
            $this->addFlash('success', 'Article modifié avec succès');
            return $this->redirectToRoute('admin.article.index');
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form'     => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name="admin.article.delete", methods="DELETE")
     * @param Article $article
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Article $article, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->get('_token'))) {
            $this->em->remove($article);
            $this->em->flush();
            $this->addFlash('success', 'Article supprimé avec succès');
        }
        return $this->redirectToRoute('admin.article.index');
    }
}
