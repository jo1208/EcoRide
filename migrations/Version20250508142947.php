<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250508150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table manually';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(180) NOT NULL,
    roles JSON NOT NULL,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    telephone VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    date_naissance DATE NOT NULL,
    photo BYTEA DEFAULT NULL,
    pseudo VARCHAR(255) NOT NULL,
    is_suspended BOOLEAN NOT NULL,
    is_chauffeur BOOLEAN NOT NULL,
    is_passager BOOLEAN NOT NULL,
    note DOUBLE PRECISION DEFAULT NULL,
    credits INT NOT NULL
);
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
