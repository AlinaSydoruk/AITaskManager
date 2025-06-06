<?php

namespace App\Command;

use App\Client\Telegram\TelegramBotClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'telegram:set-webhook',
    description: 'Set Telegram Bot Webhook',
)]
class SetTelegramWebhookCommand extends Command
{
    public function __construct(
        private readonly TelegramBotClient $telegramBotClient
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $webhookUrl = 'https://bd45-83-76-190-5.ngrok-free.app/bot/webhook';
        $this->telegramBotClient->setWebhook($webhookUrl);
        $output->writeln("Webhook set to: $webhookUrl");

        return Command::SUCCESS;
    }
}
