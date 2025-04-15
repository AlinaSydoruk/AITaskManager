<?php

namespace App\Service;

use App\Repository\BoardRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigGlobalBoardsSubscriber implements EventSubscriberInterface
{
    private BoardRepository $boardRepository;
    private Environment $twig;
    private Security $security;

    public function __construct(BoardRepository $boardRepository, Environment $twig, Security $security)
    {
        $this->boardRepository = $boardRepository;
        $this->twig = $twig;
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user) {
            $boards = $this->boardRepository->findByUser($user);
            $this->twig->addGlobal('boards', $boards);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}