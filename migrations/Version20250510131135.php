<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510131135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // La table covoiturage_user existe déjà, donc on ne la recrée pas
        // $this->addSql('CREATE TABLE ...');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS covoiturage_user');
    }
}
