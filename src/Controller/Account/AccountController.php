<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Comments;
use App\Repository\UserRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'account_dashboard')]
    public function index(CommentsRepository $commentsRepository): Response
    {

        return $this->render('account/index.html.twig', [
            'comments' => $commentsRepository->findBy(['fk_user' => $this->getUser()]),
        ]);
    }

    #[Route('/profile', name: 'user_profile')]
    public function profile(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->find($this->getUser()->getId());
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($user->getPassword() !== null) {
                $plaintextPassword = $user->getPassword();
                $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('account_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/profile.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_comments_delete', methods: ['POST'])]
    public function delete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('account_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'delete_account', methods: ['POST'])]
    public function deleteAccount(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $user = $userRepository->find($id);
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
