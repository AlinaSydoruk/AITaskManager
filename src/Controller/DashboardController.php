<?php

namespace App\Controller;


use App\Repository\BoardRepository;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard', name: 'app_dashboard_')]
class DashboardController extends AbstractController
{
    public function __construct(
        private BoardRepository                   $boardRepository,
        private EntityManagerInterface            $entityManager,
        private readonly TranslatorInterface      $translator,
        private readonly TaskService              $taskService,
        private readonly TaskRepository           $taskRepository
    )
    {
    }



    #[Route('/kanban', name: 'kanban')]
    public function kanban(): Response
    {
        $tasks = $this->taskRepository->findBy(['user' => $this->getUser()]);

        $grouped = $this->taskService->getSortedTasksByStatus($tasks);

        return $this->render('kanban/index.html.twig', [
            'tasksByStatus' => $grouped,
        ]);
    }





    #[Route('/{id}', name: 'show')]
    public function show(string $id): Response
    {
        $board = $this->boardRepository->find($id);
        return $this->render('board/show.html.twig', [
            'board' => $board,
            'boardId' => $board->getId(),
            ]);
    }

}
