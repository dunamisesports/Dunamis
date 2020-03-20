<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\EditUserType;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
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
     * @Route("/admin/user", name="index.admin.user")
     */
    public function index()
    {
        $user = $this->repository->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users'      => $user
        ]);
    }

    /**
     * @Route("/admin/register", name="user.admin.register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder):Response
    {
        $user = new  User();
        $form =  $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $role = $user->getRoles();
            $user->setRoles($role);
            $user->setValidate(true);
            $this->em->persist($user);
            $this->em->flush();

            return  $this->redirectToRoute("index.admin.user");
        }
        return $this->render('admin/user/createUser.html.twig', [
            'form'              => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/modifier/{id}", name="user.admin.edit", methods="GET|POST")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editUser(User $user, Request $request):Response
    {
        $form = $this->createForm(EditUserType::class, $user);
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
     * @Route("/admin/supprimer/{id}", name="user.admin.delete")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function deleteUser(User $user, Request $request):Response
    {
        if($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token')))
        {
            $this->em->remove($user);
            $this->em->flush();
        }
        $this->addFlash('success', 'L\'utilisateur  a bien été Supprimer');
        return $this->redirectToRoute('index.admin.user');
    }
}
