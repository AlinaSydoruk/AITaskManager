<?php

namespace App\Controller;

use App\Client\AI\OpenAITaskClient;
use App\Entity\TelegramChat;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\BotTaskCreationService;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class TelegramWebhookController extends AbstractController
{



    public function __construct(
        private readonly OpenAITaskClient         $openAITaskClient,
        private EntityManagerInterface            $entityManager,
        private UserRepository                    $userRepository,
        private readonly TranslatorInterface      $translator,
        private readonly TaskService              $taskService,
        private readonly BotTaskCreationService   $taskCreationService,
        private readonly LoggerInterface $logger,

    )
    {
    }


    #[Route('/bot/webhook', name: 'telegram_webhook', methods: ['POST'])]
    public function handle(Request $request): JsonResponse
    {
        $raw = $request->getContent();
        file_put_contents(__DIR__ . '/../../var/log/telegram_debug.log', print_r([
            'payload' => $raw
        ], true), FILE_APPEND);

        $data = json_decode($raw, true);

        // ✅ Ловим только сообщения (а не service events)
        if (!isset($data['message']) || !isset($data['message']['text'])) {
            return new JsonResponse(['info' => 'Not a text message'], 200);
        }

        $message = (string) $data['message']['text'];
        $fromId = $data['message']['from']['id'] ?? null;
        $chatId = $data['message']['chat']['id'] ?? null;
        $chatType = $data['message']['chat']['type'] ?? null;


        // ✅ Не останавливаем webhook на ошибке валидации — возвращаем 200
        if (!$message || !$fromId || !$chatType || !$chatId) {
            $this->logger->warning('Invalid message structure', ['data' => $data]);
            return new JsonResponse(['info' => 'Invalid structure'], 200);
        }

        if (!in_array($chatType, ['group', 'supergroup'], true)) {
            return new JsonResponse(['info' => 'Ignored non-group message'], 200);
        }




        $chat = $this->entityManager->getRepository(TelegramChat::class)->find((string) $chatId);
        if (!$chat) {
            $this->logger->warning('Chat not registered', ['chat_id' => $chatId]);
            return new JsonResponse(['info' => 'Chat not registered'], 200);
        }

        $users = $chat->getUsers()->toArray();
        if (empty($users)) {
            $this->logger->warning('No users in chat', ['chat_id' => $chatId]);
            return new JsonResponse(['info' => 'No users linked'], 200);
        }

        $allowedSenderIds = array_filter($users, fn(User $u) => $u->getTelegramId());
        $allowedSenderIds = array_map(fn(User $u) => (int) $u->getTelegramId(), $allowedSenderIds);

        if (!in_array($fromId, $allowedSenderIds, true)) {
            return new JsonResponse(['info' => 'Sender not allowed'], 200);
        }



        try {
            $createdTasks = $this->taskCreationService->createTasksForUsers($users, $message);

            foreach ($createdTasks as $task) {
                $this->entityManager->persist($task);
            }
            $this->entityManager->flush();

            return new JsonResponse([
                'status' => 'ok',
                'created_count' => count($createdTasks),
                'task_titles' => array_map(fn($task) => $task->getTitle(), $createdTasks),
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Webhook processing failed', ['exception' => $e]);
            // ⚠️ Даже при ошибке — вернуть 200, чтобы Telegram не дублировал update
            return new JsonResponse(['info' => 'Error, but acknowledged'], 200);
        }
    }






}
