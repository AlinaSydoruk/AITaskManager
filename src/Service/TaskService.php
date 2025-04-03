<?php

namespace App\Service;

use App\Entity\Enom\TaskStatus;

class TaskService
{

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


}