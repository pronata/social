<?php

namespace App\Command;

use Random\Randomizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:replace',
    description: 'Замена данных'
)]
class ReplaceCsvDataCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $input = fopen(dirname(__DIR__, 2).'/var/people.csv', 'r');
        $output = fopen(dirname(__DIR__, 2).'/var/temp.csv', 'w');//open for reading

        $newData = [];
        while( false !== ( $data = fgetcsv($input) ) )
        {
            $name = explode(' ', $data[0]);

            $newData[0] = (string) Uuid::v4();
            $newData[1] = $name[1];
            $newData[2] = $name[0];
            $newData[3] = (new \DateTimeImmutable())->modify('-'.$data[1].' years '.(new Randomizer())->getInt(1, 364).' days')->format('Y-m-d');
            $newData[4] = array_rand(array_flip(['танцы', 'садоводство', 'йога', 'фотография', 'воллейбол', 'рисование', 'футбол', 'писательство', 'программирование']));
            $newData[5] = array_rand(array_flip(['Барнаул', 'Волчанск', 'Дмитровск', 'Ейск', 'Елизово', 'Каменск-Шахтинский', 'Краснодар', 'Назарово', 'Нижний Новгород', 'Новый Уренгой', 'Владивосток']));
            $newData[6] = '$2y$13$GvklEt2usEk.Sjrmji1k.eolCK/w7zobfxWuyZ23hy55zItJkdwJK';
            $newData[7] = '{}';


            //write modified data to new file
            fputcsv( $output, $newData);
        }

        //close both files
        fclose( $input );
        fclose( $output );

        return Command::SUCCESS;
    }
}