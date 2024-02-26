<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:download',
    description: 'Скачивание файла'
)]
class DownloadCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url =
            'https://raw.githubusercontent.com/OtusTeam/highload/master/homework/openapi.json';

        // Use basename() function to return the base name of file
        $fileName = basename($url);


        // Use file_get_contents() function to get the file
        // from url and use file_put_contents() function to
        // save the file by using base name
        if (file_put_contents($fileName, file_get_contents($url)))
        {
            echo "File downloaded successfully";
        }
        else
        {
            echo "File downloading failed.";
        }

        return Command::SUCCESS;
    }
}