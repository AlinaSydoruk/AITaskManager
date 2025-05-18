<?php

namespace App\Service;

use App\Client\AI\OpenAITaskClient;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BotTaskCreationService
{

    public function __construct(
        private readonly OpenAITaskClient $openAITaskClient,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @throws \DateMalformedStringException
     */
    public function createTaskForUserFromMessage(User $user, string $rawMessage): Task
    {
        $parsed = $this->openAITaskClient->messageToTask($rawMessage);

        $task = new Task(userId: $user->getId());
        $task->setTitle($parsed['title']);
        $task->setDescription($parsed['description'] ?? null);
        $task->setDeadline(!empty($parsed['deadline']) ? new \DateTime($parsed['deadline']) : null);
        $task->setApproximateEstimate((int)$parsed['estimate']);


        $this->em->persist($task);

        return $task;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function createTasksForUsers(array $users, string $message): array
    {
        $tasks = [];

        foreach ($users as $user) {
            $tasks[] = $this->createTaskForUserFromMessage($user, $message);
        }

        $this->em->flush();

        return $tasks;
    }

}