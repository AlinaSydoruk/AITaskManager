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

}