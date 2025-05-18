<?php

namespace App\Client\Telegram;

readonly class TelegramConfig
{
    public function __construct(
        private  string $accessToken,
        private  string $apiUrl,

    )
    {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }


}