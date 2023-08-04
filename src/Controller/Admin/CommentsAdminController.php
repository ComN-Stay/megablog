<?php

namespace App\Controller\Admin;

use App\Entity\Comments;
use App\Form\CommentsType;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/comments')]
class CommentsAdminController extends AbstractController
{
    #[Route('/', name: 'app_comments_admin_index', methods: ['GET'])]
    public function index(CommentsRepository $commentsRepository): Response
    {
        return $this->render('admin/comments_admin/index.html.twig', [
            'comments' => $commentsRepository->findBy(['status' => 0]),
        ]);
    }

    #[Route('/valid/{id}', name: 'app_comments_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setStatus(true);
        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_comments_admin_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/{id}', name: 'app_comments_admin_delete', methods: ['GET'])]
    public function delete(Request $request, Comments $comment, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_comments_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
