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
        private BoardRepository                   $boardRepository,
        private TaskService                       $taskService,
        private TaskRepository                    $taskRepository,
        private EntityManagerInterface            $entityManager,
        private readonly TranslatorInterface      $translator,
        private readonly SubcategoryRepository    $subcategoryRepository,
    )
    {
    }



    #[Route('/create', name: 'create')]
    public function create(Request $request, int $boardId): Response
    {
        // TODO не правильно определяет subfolder в которой лежит
        $parentId = $request->get('parentId') ?: null;
        $task = new Task();

        $parentSubcategory = null;
        if ($parentId){
            $parentSubcategory = $this->subcategoryRepository->find($parentId);
        }
        $board = $this->boardRepository->find($boardId);
        if(!$board){
            $this->addFlash('error', $this->translator->trans('error.folder_not_found'));
            return $this->redirectToRoute('app_home');
        }
        $task->setSubcategory($parentSubcategory);
        $task->setBoard($board);

        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            $days = $form->get('approximateEstimateDays')->getData();
            $time = $form->get('approximateEstimateHours')->getData();

            $days = is_numeric($days) && $days >= 0 ? (int)$days : 0;

            if ($time instanceof \DateTimeInterface) {
                $hours = (int)$time->format('H');
                $minutes = (int)$time->format('i');
            } else {
                $hours = 0;
                $minutes = 0;
            }

            $estimatedTime = $this->taskService->getEstimateTimeInMinutes($days, $hours, $minutes);
            $task->setApproximateEstimate((string)$estimatedTime);

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            if ($parentSubcategory) {
                return $this->redirectToRoute('app_subcategory_show', [
                    'id' => $parentSubcategory->getId(),
                    'boardId' => $boardId,
                ]);
            }

            return $this->redirectToRoute('app_board_index', [
                'id' => $boardId,
            ]);

        }

        return $this->render('task/create.html.twig', [
            'form' => $form,
            'boardId' => $boardId,
            'parentId' => $parentId
        ]);

    }



    #[Route('/{id}', name: 'index')]
    public function index(string $id): Response
    {
        $board = $this->boardRepository->find($id);
        return $this->render('board/index.html.twig', [
            'board' => $board,
            'boardId' => $board->getId(),
            ]);
    }

}
