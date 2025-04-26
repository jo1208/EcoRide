<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:mailer-test',
    description: 'Teste l\'envoi d\'un email et vérifie la configuration SMTP.',
)]
class TestMailerCommand extends Command
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>📨 Envoi de mail de test...</info>");

        try {
            $email = (new Email())
                ->from('ecoride.dev@gmail.com')
                ->to('jonathan.pina1208@gmail.com') // <-- Adresse fixée ici
                ->subject('🚀 Test d\'envoi de mail depuis Symfony')
                ->html('<p>Si tu vois ce message, ton mailer est bien configuré 🎯</p>');

            $this->mailer->send($email);

            $output->writeln('<info>✅ Email envoyé avec succès ! Vérifie ta boîte ✉️</info>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Erreur lors de l\'envoi : ' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }
}
