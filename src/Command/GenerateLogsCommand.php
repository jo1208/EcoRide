<?php

namespace App\Command;

use App\Document\ConnectionLog;
use Doctrine\ODM\MongoDB\DocumentManager;
use Faker\Factory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'generate:logs',
    description: 'Génère des logs de connexion factices dans MongoDB.',
)]
class GenerateLogsCommand extends Command
{
    public function __construct(private DocumentManager $dm)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('count', InputArgument::OPTIONAL, 'Nombre de logs à générer', 20);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = (int) $input->getArgument('count');
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < $count; $i++) {
            $log = new ConnectionLog();
            $log->setUserId((string) $faker->numberBetween(1000, 9999));
            $log->setUsername($faker->email());
            $log->setIp($faker->ipv4());
            $log->setSuccess($faker->boolean(85));
            $log->setTimestamp($faker->dateTimeBetween('-30 days', 'now'));

            $this->dm->persist($log);
        }

        $this->dm->flush();

        $output->writeln("✅ $count logs insérés avec succès.");
        return Command::SUCCESS;
    }
}
