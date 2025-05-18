<?php

namespace App\Controller;

use App\Client\AI\OpenAITaskClient;
use App\Domain\User\Model\User;
use App\Entity\TelegramChat;
use App\Repository\UserRepository;
use App\Service\BotTaskCreationService;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;
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
        private readonly LoggerInterface $logger
    )
    {
    }


    #[Route('/bot/webhook', name: 'telegram_webhook', methods: ['POST'])]
    public function handle(Request $request): JsonResponse
    {
        error_log(">>> TELEGRAM HANDLE CALLED <<<");

        $data = json_decode($request->getContent(), true);
        $this->logger->info('Telegram webhook triggered', $data);


        $message = $data['message']['text'] ?? null;
        $fromId = $data['message']['from']['id'] ?? null;
        $chatType = $data['message']['chat']['type'] ?? null;

        if (!$message || !$fromId || !$chatType) {
            return new JsonResponse(['error' => 'Invalid message'], 400);
        }

        if (!in_array($chatType, ['group', 'supergroup'], true)) {
            return new JsonResponse(['info' => 'Ignoring non-group messages'], 200);
        }

        $allowedSenderIds = [1948287534]; // замените на реального преподавателя

        if (!in_array($fromId, $allowedSenderIds, true)) {
            return new JsonResponse(['info' => 'Sender not allowed'], 200);
        }

        $chatId = $data['message']['chat']['id'] ?? null;

        $chat = $this->entityManager->getRepository(TelegramChat::class)->find($chatId);

        if (!$chat) {
            return new JsonResponse(['error' => 'Chat not registered in system'], 404);
        }

        $users = $chat->getUsers()->toArray();


        if (empty($users)) {
            return new JsonResponse(['error' => 'No users linked to this chat'], 404);
        }


        try {
            $createdTasks = $this->taskCreationService->createTasksForUsers($users, $message);

            return new JsonResponse([
                'status' => 'ok',
                'created_count' => count($createdTasks),
                'task_titles' => array_map(fn($task) => $task->getTitle(), $createdTasks),
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Failed to create tasks',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


}
