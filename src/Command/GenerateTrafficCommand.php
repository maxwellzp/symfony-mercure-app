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
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[AsCommand(
    name: 'app:generate-traffic',
    description: 'Add a short description for your command',
)]
class GenerateTrafficCommand extends Command
{
    public function __construct(private readonly HubInterface $hub)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $markets = ['BTC-USD', 'ETH-USD', 'LTC-USD'];
            while (true) {
                foreach ($markets as $market) {
                    $rate = rand(50000, 60000);
                    $timestamp = time();

                    sleep(1);
                    $update = new Update(
                        'https://example.com/exchange-rate/' . $market,
                        json_encode(['rate' => $rate])
                    );
                    $this->hub->publish($update);

                    $update = new Update(
                        'https://example.com/exchange-rates',
                        json_encode([
                            'market' => $market,
                            'rate' => $rate,
                            'timestamp' => $timestamp,
                        ])
                    );
                    $this->hub->publish($update);
                }
            }

        return Command::SUCCESS;
    }
}
