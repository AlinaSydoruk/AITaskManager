<?php

namespace App\Controller;

use App\Domain\User\Model\User;
use App\Entity\Board;
use App\Entity\Subcategory;
use App\Entity\Task;
use App\Form\BoardType;
use App\Form\TaskFormType;
use App\Repository\BoardRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/board/{boardId}', name: 'app_task_')]
class TaskController extends AbstractController
{
    public function __construct(
        private BoardRepository                $boardRepository,
        private TaskService                    $taskService,
        private TaskRepository                 $taskRepository,
        private EntityManagerInterface         $entityManager,
        private readonly TranslatorInterface   $translator,
        private readonly SubcategoryRepository $subcategoryRepository,
    )
    {
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, int $boardId): Response
    {
        $parentId = $request->get('parentId') ?: null;
        $task = new Task();

        $board = $this->boardRepository->find($boardId);
        if (!$board) {
            return new Response("Board with id: $boardId does not exist", Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(TaskFormType::class, $task);

        $subcategory = null;

        if ($parentId) {
            $subcategory = $this->subcategoryRepository->find($parentId);
            $form->get('subcategory')->setData($parentId);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setBoard($board);

            //  set subcategory
            $subcategoryId = $form->get('subcategory')->getData();
            $subcategory = $this->subcategoryRepository->find($subcategoryId);
            if ($subcategory) {
                $task->setSubcategory($subcategory);
            }

            // deadline
            $deadline_date = $form->get('deadline_date')->getData();
            $deadline_time = $form->get('deadline_time')->getData();

            if($deadline_date && $deadline_time) {
                $deadline = \DateTime::createFromFormat('Y-m-d H:i:s',
                    $deadline_date->format('Y-m-d') . ' ' . date('H:i:s', $deadline_time)
                );

                $task->setDeadline($deadline);
            }

            // scheduled for
            $scheduledDate = $form->get('scheduled_date')->getData();
            $scheduledTime = $form->get('scheduled_time')->getData();

            if ($scheduledDate && $scheduledTime) {
                $scheduled = \DateTime::createFromFormat('Y-m-d H:i:s',
                    $scheduledDate->format('Y-m-d') . ' ' . date('H:i:s', $scheduledTime)
                );

                $task->setScheduledForDate($scheduled);
            }

            // EstimateType
            $estimate = $form->get('estimate')->getData();
            $days = (int)($estimate['days'] ?? 0);
            $time = $estimate['time'] ?? null;

            $hours = $minutes = 0;
            if ($time instanceof \DateTimeInterface) {
                $hours = (int)$time->format('H');
                $minutes = (int)$time->format('i');
            }

            $estimatedTime = $this->taskService->getEstimateTimeInMinutes($days, $hours, $minutes);
            $task->setApproximateEstimate((string)$estimatedTime);

            $this->entityManager->persist($task);
            $this->entityManager->flush();


            if ($subcategory) {
                return $this->redirectToRoute('app_subcategory_show', [
                    'id' => $subcategory->getId(),
                    'boardId' => $boardId,
                ]);
            }

            return $this->redirectToRoute('app_board_show', [
                'id' => $boardId,
            ]);
        }

        return $this->render('task/create.html.twig', [
            'form' => $form,
            'boardId' => $boardId,
            'parentId' => $parentId,
            'subcategory' =>$subcategory
        ]);
    }


    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Request $request, int $boardId, int $id): Response
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            return new Response("Task with id: $id does not exist", Response::HTTP_NOT_FOUND);
        }

        $board = $this->boardRepository->find($boardId);
        if (!$board) {
            return new Response("Board with id: $boardId does not exist", Response::HTTP_NOT_FOUND);
        }


        $form = $this->createForm(TaskFormType::class, $task);

        if ($task->getSubcategory()) {
            $form->get('subcategory')->setData($task->getSubcategory()->getId());
        }

        $estimateMinutes = (int)$task->getApproximateEstimate();
        [$days, $hours, $minutes] = $this->taskService->convertMinutesToEstimateParts($estimateMinutes);

        $form->get('estimate')->get('days')->setData($days);
        $form->get('estimate')->get('time')->setData(
            (new \DateTime())->setTime($hours, $minutes)
        );

        if ($task->getDeadline()) {
            $form->get('deadline_date')->setData($task->getDeadline());
            $form->get('deadline_time')->setData($task->getDeadline()->getTimestamp());
        }

        if ($task->getScheduledForDate()) {
            $form->get('scheduled_date')->setData($task->getScheduledForDate());
            $form->get('scheduled_time')->setData($task->getScheduledForDate()->getTimestamp());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            // Обновляем подкатегорию по ID из скрытого поля
            $subcategoryId = $form->get('subcategory')->getData();
            $subcategory = null;
            if ($subcategoryId) {
                $subcategory = $this->subcategoryRepository->find($subcategoryId);
                $task->setSubcategory($subcategory);
            }

            // deadline
            $deadline_date = $form->get('deadline_date')->getData();
            $deadline_time = $form->get('deadline_time')->getData();

            if($deadline_date && $deadline_time) {
                $deadline = \DateTime::createFromFormat('Y-m-d H:i:s',
                    $deadline_date->format('Y-m-d') . ' ' . date('H:i:s', $deadline_time)
                );

                $task->setDeadline($deadline);
            }

            // scheduled for
            $scheduledDate = $form->get('scheduled_date')->getData();
            $scheduledTime = $form->get('scheduled_time')->getData();

            if ($scheduledDate && $scheduledTime) {
                $scheduled = \DateTime::createFromFormat('Y-m-d H:i:s',
                    $scheduledDate->format('Y-m-d') . ' ' . date('H:i:s', $scheduledTime)
                );

                $task->setScheduledForDate($scheduled);
            }



            // Обновляем estimate
            $estimate = $form->get('estimate')->getData();
            $days = (int)($estimate['days'] ?? 0);
            $time = $estimate['time'] ?? null;

            $hours = $minutes = 0;
            if ($time instanceof \DateTimeInterface) {
                $hours = (int)$time->format('H');
                $minutes = (int)$time->format('i');
            }

            $estimatedTime = $this->taskService->getEstimateTimeInMinutes($days, $hours, $minutes);
            $task->setApproximateEstimate((string)$estimatedTime);

            $this->entityManager->flush(); // persist не нужен — объект уже в БД

            // Редирект в подкатегорию, если есть
            if ($subcategory) {
                return $this->redirectToRoute('app_task_show', [
                    'id' => $id,
                    'boardId' => $boardId,
                ]);
            }

            return $this->redirectToRoute('app_board_show', [
                'id' => $boardId,
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'boardId' => $boardId,
            'parentId' => $task->getSubcategory()?->getId(),
            'task' => $task
        ]);
    }


    #[Route('/{id}', name: 'show')]
    public function show(string $id, string $boardId): Response
    {
        $board = $this->boardRepository->find($boardId);
        if (!$board) {
            return new Response("Board with id : $boardId does not exist", Response::HTTP_NOT_FOUND);
        }

        $task = $this->taskRepository->find($id);
        if (!$task) {
            return new Response("Task with id : $id does not exist", Response::HTTP_NOT_FOUND);
        }

        return $this->render('task/show.html.twig', [
            'boardId' => $boardId,
            'task' => $task,
            'subcategory' => $task->getSubcategory(),
        ]);
    }


    #[Route('/delete/{id}', name: 'delete')]
    public function delete(string $id, string $boardId): Response
    {

        $board = $this->boardRepository->find($boardId);

        $task = $this->taskRepository->find($id);
        if (!$task) {
            return new Response("Task with id : $id does not exist", Response::HTTP_NOT_FOUND);
        }
        $subcategory = $task->getSubcategory();

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        // Редирект в подкатегорию, если есть
        if ($subcategory) {
            return $this->redirectToRoute('app_subcategory_show', [
                'id' => $subcategory->getId(),
                'boardId' => $boardId,
            ]);
        }
        if ($board) {
            return $this->redirectToRoute('app_board_show', [
                'id' => $boardId,
            ]);
        }
        return $this->redirectToRoute('app_home');

    }
}
