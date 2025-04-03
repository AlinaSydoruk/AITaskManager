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

}