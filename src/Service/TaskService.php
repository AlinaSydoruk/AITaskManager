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

    public function getEstimateTimeInMinutes(int $hours, int $minutes): int
    {
        // Игнорируем days и считаем только часы и минуты
        return ($hours * 60) + $minutes;
    }

    public function convertMinutesToEstimateParts(int $totalMinutes): array
    {
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return [$hours, $minutes];
    }

    public function getAllTasksBelongToUser(User $user) : array
    {
        return $this->taskRepository->findAllByUserId($user->getId());
    }

    public function getScheduledTasks(User $user): array
    {
        return array_filter(
            $this->getAllTasksBelongToUser($user),
            fn($task) => $task->getScheduledForDate() !== null
        );
    }

    public function getUnscheduledTasks(User $user): array
    {
        return array_filter(
            $this->getAllTasksBelongToUser($user),
            fn($task) => $task->getScheduledForDate() === null
        );
    }

}