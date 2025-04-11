<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\BinanceWebSocketService;

#[AsCommand(
    name: 'app:binance-websocket',
    description: 'Command to start websocket service',
)]
class BinanceWebsocketCommand extends Command
{
    public function __construct(private BinanceWebSocketService $webSocketService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('Listening to Binance socket.');

        $this->webSocketService->listen();
        return Command::SUCCESS;
    }
}
