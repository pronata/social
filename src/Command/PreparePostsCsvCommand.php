<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:prepare-posts',
    description: 'Заполнение клиентов файла'
)]
class PreparePostsCsvCommand extends Command
{
    public function __construct(
        string $name = null,
    ) {
        parent::__construct($name);
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contents = file_get_contents(dirname(__DIR__, 2).'/var/posts.txt');
        $posts = explode(".", $contents);

        $output->writeln(count($posts));

       // $output = fopen(dirname(__DIR__, 2).'/var/posts.csv', 'w');

        return Command::SUCCESS;
    }
}