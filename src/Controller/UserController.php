<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
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
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/inscription", name="user.register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder):Response
    {
        $user = new  User();
        $form =  $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $this->em->persist($user);
            $this->em->flush();

            return  $this->redirectToRoute("mailer.user.confime",
                [
                    'id'        => $user->getId(),
                ]);
        }
        return $this->render('user/userCreate.html.twig', [
            'form'              => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/validate/{id}", name="user.validate")
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validatUser(User $user, Request $request)
    {
        if($user->getValidate() == null)
        {
            $user->setValidate(true);

            $this->em->flush();

            return $this->redirectToRoute('user.information',
                [
                    'id'      => $user->getId(),
                ]);
        }
    }

    /**
     * @Route("modifier/{id}", name="user.edit", methods="GET|POST")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function userEdite(User $user, Request $request):Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', "L\'utilisateur  a bien été Modifier");
            return $this->redirectToRoute('index.admin.user');
        }
        return $this->render('admin/user/editUser.html.twig',
            [
                'form'      => $form->createView(),
                'user'      => $user,
            ]);
    }

    /**
     * @Route("user/information/{id}", name="user.information")
     * @param User $user
     */
    public function informationUser(User $user): Response
    {
        return $this->render('user/information.html.twig',
            [
                'user'      => $user
            ]);
    }
}
