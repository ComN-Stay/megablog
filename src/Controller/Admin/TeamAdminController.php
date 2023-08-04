<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/team')]
class TeamAdminController extends AbstractController
{
    #[Route('/', name: 'app_team_admin_index', methods: ['GET'])]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('admin/team_admin/index.html.twig', [
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_team_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $team = new Team();
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $team->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($team, $plaintextPassword);
            $team->setPassword($hashedPassword);
            $team->setRoles(['ROLE_ADMIN']);
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('app_team_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/team_admin/new.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_admin_show', methods: ['GET'])]
    public function show(Team $team): Response
    {
        return $this->render('admin/team_admin/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_team_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Team $team, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($team->getPassword() !== null) {
                $plaintextPassword = $team->getPassword();
                $hashedPassword = $passwordHasher->hashPassword($team, $plaintextPassword);
                $team->setPassword($hashedPassword);
            }
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('app_team_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/team_admin/edit.html.twig', [
            'team' => $team,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_team_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Team $team, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$team->getId(), $request->request->get('_token'))) {
            $entityManager->remove($team);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_team_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
