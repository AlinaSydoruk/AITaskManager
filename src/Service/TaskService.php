<?php

namespace App\Service;

use App\Entity\Enom\TaskStatus;
use App\Entity\User;
use App\Repository\BoardRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class TaskService
{
    public function __construct(
        private BoardRepository                   $boardRepository,
        private readonly TranslatorInterface      $translator,
        private readonly TaskRepository           $taskRepository,
        private readonly Security                 $security,
    )
    {
    }

    public function getSortedTasksByStatus(array $tasks):array
    {
        $sortedByStatusTasks = [];
        foreach (TaskStatus::cases() as $status){
            $sortedByStatusTasks[$status->value]=[];
        }
        foreach ($tasks as $task) {
            $taskStatus = $task->getTaskStatus()->value;
            $sortedByStatusTasks[$taskStatus][] = $task;
        }
        return $sortedByStatusTasks;
    }

    public function getEstimateTimeInMinutes(int $days, int $hours , int $minutes ) :int
    {
        return $days * 24 * 60 + $hours * 60 + $minutes;
    }

    public function convertMinutesToEstimateParts(int $totalMinutes): array
    {
        $days = intdiv($totalMinutes, 1440); // 1440 = 24 * 60
        $remainingMinutes = $totalMinutes % 1440;

        $hours = intdiv($remainingMinutes, 60);
        $minutes = $remainingMinutes % 60;

        return [$days, $hours, $minutes];
    }

    public function getAllTasksBelongToUser(User $user) : array
    {
        return $this->taskRepository->findAllByUserId($user->getId());
    }

    public function getUnscheduledTasks(User $user):array
    {
        $unscheduledTasks=[];
        $boards = $this->boardRepository->findByUser($user);
        foreach ($boards as $board) {
            foreach ($board->getTasks() as $task) {
                if (!$task->getScheduledForDate()) {
                    $unscheduledTasks[] = $task;
                }

            }
        }
        return $unscheduledTasks;
    }

}