<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
    private $repository;
    private $em;

    /**
     * ArticleController constructor.
     * @param $repository
     * @param $em
     */
    public function __construct(UserRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/message/inscription/{id}", name="mailer.user.confime")
     * @param \App\Controller\MailerInterface $mailer
     * @param Request $request
     * @param User $user
     */
    public function sendEmail(MailerInterface $mailer, Request $request, User $user)
    {
        $email = (new  TemplatedEmail())
            ->from('jobby00dev@gmail.com')
            ->to( new Address($user->getEmail()))
            ->subject('Finalisez votre inscription')
            ->htmlTemplate('mailer/index.html.twig')
            ->context([
                'username'  => $user
            ]);
        $mailer->send($email);
    }
}
