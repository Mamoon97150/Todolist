<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listUsers(UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createUser(Request $request, UserPasswordHasherInterface $hasher)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $password = $hasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editUser(User $user, Request $request, UserPasswordHasherInterface $hasher)
    {
        $this->denyAccessUnlessGranted('edit', $user);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);


        //Add event listener for password hashing
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $hasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($password);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
