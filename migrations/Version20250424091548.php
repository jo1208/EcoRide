<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424091548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Ajoute la colonne nullable pour éviter l’erreur
        $this->addSql('ALTER TABLE covoiturage ADD created_at DATETIME DEFAULT NULL');

        // Mets à jour les anciens trajets avec la date actuelle
        $this->addSql('UPDATE covoiturage SET created_at = NOW() WHERE created_at IS NULL');

        // Puis rends la colonne obligatoire (NOT NULL)
        $this->addSql('ALTER TABLE covoiturage MODIFY created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage DROP created_at
        SQL);
    }
}
