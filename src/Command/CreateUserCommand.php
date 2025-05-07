<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Créer un utilisateur test dans la base de données.',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword($this->hasher->hashPassword($user, 'test1234'));
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setPseudo('testuser');
        $user->setAdresse('123 rue Test');
        $user->setTelephone('0102030405');
        $user->setDateNaissance(new \DateTime('1990-01-01'));
        $user->setIsChauffeur(false);
        $user->setIsPassager(true);
        $user->setIsSuspended(false);
        $user->setCredits(20);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('Utilisateur créé avec succès !');

        return Command::SUCCESS;
    }
}
