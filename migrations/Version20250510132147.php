<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250510132147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création manuelle de la table pivot covoiturage_user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_tables WHERE tablename = 'covoiturage_user'
                ) THEN
                    CREATE TABLE covoiturage_user (
                        covoiturage_id INT NOT NULL,
                        user_id INT NOT NULL,
                        PRIMARY KEY(covoiturage_id, user_id),
                        CONSTRAINT FK_COVOITURAGE FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE,
                        CONSTRAINT FK_USER FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
                    );
                END IF;
            END
            $$;
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS covoiturage_user');
    }
}
