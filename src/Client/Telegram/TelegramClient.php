<?php

namespace App\Client\Telegram;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class TelegramClient
{
    public function __construct(
        protected readonly HttpClientInterface  $httpClient,
        protected readonly TelegramConfig       $telegramConfig,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function submit(string $method, string $url , array $body): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->httpClient->request($method, $url, $body);

    }


}