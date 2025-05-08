<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250508144639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
        CREATE TABLE preference (
            id SERIAL PRIMARY KEY,
            fumeur BOOLEAN DEFAULT NULL,
            animal BOOLEAN DEFAULT NULL,
            musique BOOLEAN DEFAULT NULL,
            autres TEXT DEFAULT NULL,
            user_id INT NOT NULL UNIQUE,
            CONSTRAINT FK_PREFERENCE_USER FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
        )
    ');
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
    }
}
