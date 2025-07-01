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

    protected function submit(array $messages): array
    {

        try {
            $response = $this->client->chat()->create([
                'model' => $this->openAIConfig->getModel(),
                'messages' => $messages,
            ]);


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


    protected function buildMessage(string $systemContent, string $userContent):array
    {
        return  [
            ['role' => 'system', 'content' => $systemContent],
            ['role' => 'user', 'content' => $userContent],
        ];
    }
}