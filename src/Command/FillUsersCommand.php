<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:fill-users',
    description: 'Заполнение клиентов файла'
)]
class FillUsersCommand extends Command
{
    public function __construct(private readonly Connection $connection, string $name = null)
    {
        parent::__construct($name);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // testers
        var_dump($this->connection->executeQuery("SELECT * from social_user limit 1;")->fetchFirstColumn());

        return Command::SUCCESS;
    }
}