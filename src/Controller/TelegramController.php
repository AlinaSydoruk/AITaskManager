<?php

namespace App\Controller;


use App\Client\Telegram\TelegramBotClient;
use App\Entity\Board;
use App\Entity\Subcategory;
use App\Entity\TelegramChat;
use App\Entity\User;
use App\Form\BoardType;
use App\Form\SubcategoryType;
use App\Form\SubscribeTelegramType;
use App\Repository\BoardRepository;

use App\Repository\TaskRepository;
use App\Service\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/telegram', name: 'app_telegram_')]
class TelegramController extends AbstractController
{

    public function __construct(
        private readonly TranslatorInterface      $translator,
        private readonly TelegramBotClient         $botClient
    )
    {
    }

    #[Route('/subscribe', name: 'subscribe')]
    public function subscribe(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SubscribeTelegramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $chatId = $data['chat_id'];
            $chatTitle = $data['chat_title'] ?? 'Невідомо';
            $teacherId = $data['teacher_id'];

            $chat = $em->getRepository(TelegramChat::class)->find($chatId);
            if (!$chat) {
                $chat = new TelegramChat($chatId);
                $chat->setTitle($chatTitle);
                $em->persist($chat);
            }

            // Додаємо користувача в чат
            /** @var User $user */
            $user = $this->getUser();
            $chat->addUser($user);

            // Записуємо ID дозволеного викладача
            $user->setTelegramId((string) $teacherId); // або зроби окреме поле if needed

            $em->flush();

            $this->addFlash('success', $this->translator->trans('flash.telegram_chat_and_teacher_connected'));

            return $this->redirectToRoute('app_frontend');
        }

        return $this->render('telegram/subscribe.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/debug-chat-id', name: 'debug_chat_id')]
    public function debugChatId( ): Response
    {
        $updates = $this->botClient->getUpdates();
        $chats = [];

        foreach ($updates['result'] ?? [] as $update) {
            if (isset($update['message']['chat'])) {
                $chat = $update['message']['chat'];
                $chats[$chat['id']] = [
                    'id' => $chat['id'],
                    'title' => $chat['title'] ?? ($chat['username'] ?? 'Private Chat'),
                    'type' => $chat['type']
                ];
            }
        }

        return $this->render('telegram/debug_chat_id.html.twig', [
            'chats' => $chats,
        ]);
    }

}
