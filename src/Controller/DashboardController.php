<?php

namespace App\Controller;


use App\Entity\Enom\TaskStatus;
use App\Entity\Task;
use App\Repository\BoardRepository;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        private readonly TaskRepository           $taskRepository,
        private readonly Security                 $security,
    )
    {
    }



    #[Route('/kanban', name: 'kanban')]
    public function kanban(): Response
    {

        $tasks = $this->taskService->getAllTasksBelongToUser($this->getUser());
        $grouped = $this->taskService->getSortedTasksByStatus($tasks);

        return $this->render('dashboard/kanban.html.twig', [
            'tasksByStatus' => $grouped,
        ]);
    }

    #[Route('/calendar', name: 'calendar')]
    public function calendar(): Response
    {
        $userTasks = $this->taskService->getAllTasksBelongToUser($this->getUser());

        // Запланированные задачи
        $scheduledTasks = array_filter($userTasks, fn($task) => $task->getScheduledForDate() !== null);

        $tasks = array_values(array_map(function ($task) {
            return [
                'title' => $task->getTitle(),
                'scheduledFor' => $task->getScheduledForDate()->format('Y-m-d\TH:i:s'),
                'estimateMinutes' => (int) $task->getApproximateEstimate(),
            ];
        }, $scheduledTasks));

        // Незапланированные задачи – преобразуем тоже в массивы
        $unscheduled = array_values(array_map(function ($task) {
            return [
                'title' => $task->getTitle(),
                'estimateMinutes' => (int) $task->getApproximateEstimate(),
            ];
        }, array_filter($userTasks, fn($task) => $task->getScheduledForDate() === null)));

        return $this->render('dashboard/calendar.html.twig', [
            'tasks' => $tasks,
            'unscheduledTasks' => $unscheduled,
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



    #[Route('/task/{id}/update-status', name: 'task_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Task $task, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newStatus = $data['status'] ?? null;

        if (!$newStatus || !TaskStatus::tryFrom($newStatus)) {
            return new JsonResponse(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        }

        $task->setTaskStatus(TaskStatus::from($newStatus));
        $em->persist($task);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }


    #[Route('/calendar/events', name: 'calendar_events')]
    public function calendarEvents(): JsonResponse
    {
        $tasks = $this->taskService->getAllTasksBelongToUser($this->getUser());

        $events = array_map(function(Task $task) {
            return [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'start' => $task->getScheduledForDate()?->format('Y-m-d\TH:i:s'),
                'end' => $task->getDeadline()?->format('Y-m-d\TH:i:s'),
            ];
        }, $tasks);

        return new JsonResponse($events);
    }





}
