<?php

namespace App\Client\AI;

use App\Entity\Task;

class OpenAITaskClient extends OpenAIClient
{

    public function estimateTask(Task $task): int
    {
        $message = $this->buildMessage(
            systemContent: $this->AIPrompts::TASK_ESTIMATE,
            userContent: 'Вот задача которую необходимо оценить по времени' . json_encode($task->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        );
        $response = $this->submit($message);
        if (isset($response['estimate'])) {

            return (int)$response['estimate'];

        }


        throw new \RuntimeException('OpenAI response does not contain a valid "estimate" field.');
    }


    public function messageToTask(string $message): array
    {
        $prompt = $this->buildMessage(
            systemContent: $this->AIPrompts::MESSAGE_TO_TASK,
            userContent: 'Вот текст, содержащий задание: ' . $message,
        );

        $response = $this->submit($prompt);

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

        throw new \RuntimeException('OpenAI response does not contain a valid task object.');
    }


}