<?php

namespace App\Controller\Admin;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Service\FileUploaderService;
use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/articles')]
class ArticlesAdminController extends AbstractController
{
    #[Route('/list/{idCategorie?}', name: 'app_articles_admin_index', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository, $idCategorie): Response
    {
        return $this->render('admin/articles_admin/index.html.twig', [
            'articles' => $articlesRepository->findBy(['fk_category' => $idCategorie]),
            'categorie' => $idCategorie
        ]);
    }

    #[Route('/new', name: 'app_articles_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploaderService $file_uploader, $publicUploadDir): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) {
                    $full_path = $publicUploadDir.'/'.$file_name;
                }
                $article->setLogo($full_path);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles_admin/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_admin_show', methods: ['GET'])]
    public function show(Articles $article, CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('admin/articles_admin/show.html.twig', [
            'article' => $article,
            'category' => $categoriesRepository->find($article->getFkCategory())
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager, CategoriesRepository $categoriesRepository, FileUploaderService $file_uploader, 
        $publicUploadDir, $publicDeleteFileDir): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $uow = $entityManager->getUnitOfWork();
                $originalData = $uow->getOriginalEntityData($article);
                $logo = explode('/', $originalData['logo']);
                @unlink($publicDeleteFileDir . '/' . $logo[2]);
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) {
                    $full_path = $publicUploadDir.'/'.$file_name;
                }
                $article->setLogo($full_path);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/articles_admin/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'category' => $categoriesRepository->find($article->getFkCategory())
        ]);
    }

    #[Route('/{id}', name: 'app_articles_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_articles_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
