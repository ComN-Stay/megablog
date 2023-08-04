<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Service\FileUploaderService;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/categories')]
class CategoriesAdminController extends AbstractController
{
    #[Route('/', name: 'app_categories_admin_index', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('admin/categories_admin/index.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categories_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploaderService $file_uploader, $publicUploadDir): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) {
                    $full_path = $publicUploadDir.'/'.$file_name;
                }
                $category->setLogo($full_path);
            }
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categories_admin/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_admin_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('admin/categories_admin/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_admin_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Categories $category, 
        EntityManagerInterface $entityManager, 
        FileUploaderService $file_uploader, 
        $publicUploadDir, $publicDeleteFileDir
        ): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['logo']->getData();
            if ($file) {
                $uow = $entityManager->getUnitOfWork();
                $originalData = $uow->getOriginalEntityData($category);
                $logo = explode('/', $originalData['logo']);
                @unlink($publicDeleteFileDir . '/' . $logo[2]);
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) {
                    $full_path = $publicUploadDir.'/'.$file_name;
                }
                $category->setLogo($full_path);
            }
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categories_admin/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categories_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
