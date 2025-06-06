<?php

namespace App\Service;

use App\Client\AI\AIPrompts;
use App\Client\AI\OpenAIConfig;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OpenAI\Client;

class BotTaskCreationService
{
    public function __construct(
        private readonly Client $client,
        private readonly OpenAIConfig $openAIConfig,
        private readonly AIPrompts $AIPrompts,
        private readonly EntityManagerInterface $em
    ) {}

    public function createTaskForUserFromMessage(string $message): array
    {
        file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', print_r([
            'about_to_call_messageToTask' => (string)$message
        ], true), FILE_APPEND);

        $parsed = $this->askAI($message);

        file_put_contents(
            __DIR__ . '/../../var/log/telegram_debug.log',
            print_r(['response' => $parsed], true),
            FILE_APPEND
        );

        return $parsed;
    }

    public function createTasksForUsers(array $users, string $message): array
    {
        $detailsForTask = $this->createTaskForUserFromMessage($message);
        $tasks = [];

        foreach ($users as $user) {
            $tasks[] = $this->parseTask($user, $detailsForTask);
        }

        $this->em->flush();

        return $tasks;
    }

    private function parseTask(User $user, array $parsed): Task
    {
        $task = new Task(userId: $user->getId());
        $task->setTitle($parsed['title']);
        $task->setDescription($parsed['description'] ?? null);

        $deadline = !empty($parsed['deadline']) ? new \DateTime($parsed['deadline']) : null;
        $task->setDeadline($deadline);

        $estimate = (int)$parsed['estimate'];
        $task->setApproximateEstimate($estimate);

        $priority = \App\Entity\Enom\TaskPriority::medium;

        if ($deadline) {
            $now = new \DateTime();
            $diffInHours = ($deadline->getTimestamp() - $now->getTimestamp()) / 3600;

            if ($diffInHours < 6) {
                $priority = \App\Entity\Enom\TaskPriority::critical;
            } elseif ($diffInHours < 24) {
                $priority = \App\Entity\Enom\TaskPriority::high;
            } elseif ($diffInHours > 72) {
                $priority = \App\Entity\Enom\TaskPriority::low;
            }
        }

        $task->setTaskPriority($priority);
        $task->setTaskStatus(\App\Entity\Enom\TaskStatus::toDo);

        $this->em->persist($task);

        return $task;
    }

    public function askAI(string $message): array
    {
        file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "\n\n==== REQEST ====\n", FILE_APPEND);

        $prompt = $this->buildMessage(
            systemContent: $this->AIPrompts::MESSAGE_TO_TASK,
            userContent: 'Вот текст, содержащий задание: ' . $message,
        );

        file_put_contents(
            __DIR__ . '/../../var/log/telegram_debug.log',
            print_r(['prompt' => $prompt], true),
            FILE_APPEND
        );

        $response = $this->submit($prompt);

        file_put_contents(
            __DIR__ . '/../../var/log/telegram_debug.log',
            "\n\n==== PARSED RESPONSE ====\n" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            FILE_APPEND
        );

        if (
            isset($response['title']) &&
            isset($response['estimate']) &&
            is_numeric($response['estimate'])
        ) {
            return [
                'title' => $response['title'],
                'description' => $response['description'] ?? null,
                'deadline' => !empty($response['deadline']) ? $response['deadline'] : null,
                'estimate' => (int)$response['estimate'],
            ];
        }

        file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "❌ OpenAI response does not contain a valid task object\n", FILE_APPEND);
        throw new \RuntimeException('OpenAI response does not contain a valid task object.');
    }

    private function submit(array $messages): array
    {
        file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "\n\n====submit START====\n", FILE_APPEND);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->openAIConfig->getModel(),
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

            file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "\n\n==== RAW RESPONSE ====\n" . print_r($response, true), FILE_APPEND);

            $content = $response->choices[0]->message->content ?? null;

            if (!$content) {
                file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "\n\n==== EMPTY CONTENT ====\n", FILE_APPEND);
                throw new \RuntimeException('OpenAI did not return any message content.');
            }

            $decoded = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "\n\n==== INVALID JSON ====\n$content", FILE_APPEND);
                throw new \RuntimeException('Invalid JSON from OpenAI: ' . $content);
            }

            return $decoded;
        } catch (\Throwable $e) {
            file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', "\n\n==== EXCEPTION ====\n" . $e->getMessage(), FILE_APPEND);
            throw $e;
        }
    }

    private function buildMessage(string $systemContent, string $userContent): array
    {
        return [
            ['role' => 'system', 'content' => $systemContent],
            ['role' => 'user', 'content' => $userContent],
        ];
    }
}
