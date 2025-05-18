<?php

namespace App\Client\Telegram;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

 class TelegramBotClient extends TelegramClient
{

    private function getTelegramBotApiUrl():string
    {
        return $this->telegramConfig->getApiUrl()  . "bot" . $this->telegramConfig->getAccessToken();
    }

     /**
      * @throws TransportExceptionInterface
      */
     public function sendMessage(int $chatId, string $text): void
     {
         $url = $this->getTelegramBotApiUrl() . "/sendMessage";
         $body = [
             'json' => [
                 'chat_id' => $chatId,
                 'text' => $text,
             ]
         ];
         $this->submit("POST", $url, $body);

     }

     /**
      * @throws TransportExceptionInterface
      */
     public function setWebhook(string $url): void
     {
         $url = $this->getTelegramBotApiUrl() . "/setWebhook";
         $body = [
             'query' => ['url' => $url],
         ];

         $this->submit("POST" , $url, $body);
     }

}