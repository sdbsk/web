<?php

namespace App\Command;

use App\Service\Darujme;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'update:darujme-campaigns')]
class UpdateDarujmeCampaignsCommand extends Command
{
    public function __construct(
        private readonly Darujme $darujme,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->darujme->execute(new SymfonyStyle($input, $output));

        return Command::SUCCESS;
    }
}
