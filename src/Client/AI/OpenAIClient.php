<?php

namespace App\Client\AI;
use OpenAI\Client;

class OpenAIClient
{
    public function __construct(
        private readonly Client $client,
        private readonly OpenAIConfig $openAIConfig,
    )
    {
    }

    private function submit(array $messages):array
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' =>$messages, ]);

        return json_decode($response->choices[0]->message->content, true);
    }

    public function get(string $asRoleSystemContent, $asRoleUserContent):array
    {
        $message = [
            ['role' => 'system', 'content' => 'Ты — помощник для программиста. Отвечай кратко и по делу.'],
            ['role' => 'user', 'content' => 'Что такое SOLID?'],
        ];
        return $this->submit($message);

    }

    public function planTasks(array $tasks): array
    {
        $messages = [
        ['role' => 'system', 'content' => 'Ты — ассистент по тайм-менеджменту.'],
        ['role' => 'user', 'content' => 'Вот задачи: ' . json_encode($tasks)],
    ];
        return $this->submit($messages);
    }


}