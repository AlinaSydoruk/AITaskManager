<?php

namespace App\Client\AI;
use OpenAI\Client;

abstract class OpenAIClient
{
    public function __construct(
        protected readonly Client         $client,
        protected readonly OpenAIConfig   $openAIConfig,
        protected readonly AIPrompts      $AIPrompts
    )
    {
    }

    protected function submit(array $messages):array
    {
        $response = $this->client->chat()->create([
            'model' => $this->openAIConfig->getModel(),
            'messages' =>$messages, ]);
        return json_decode($response->choices[0]->message->content, true);
    }

    protected function buildMessage(string $systemContent, string $userContent):array
    {
        return  [
            ['role' => 'system', 'content' => $systemContent],
            ['role' => 'user', 'content' => $userContent],
        ];
    }
}