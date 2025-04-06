<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401121503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE covoiturage (id INT AUTO_INCREMENT NOT NULL, date_depart DATE NOT NULL, heure_depart TIME NOT NULL, lieu_depart VARCHAR(255) NOT NULL, date_arrivee DATE NOT NULL, heure_arrivee TIME NOT NULL, lieu_arrivee VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, nb_place INT NOT NULL, prix_personne DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE covoiturage_user (covoiturage_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F862CC4962671590 (covoiturage_id), INDEX IDX_F862CC49A76ED395 (user_id), PRIMARY KEY(covoiturage_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage_user ADD CONSTRAINT FK_F862CC4962671590 FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage_user ADD CONSTRAINT FK_F862CC49A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage_user DROP FOREIGN KEY FK_F862CC4962671590
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE covoiturage_user DROP FOREIGN KEY FK_F862CC49A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE covoiturage
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE covoiturage_user
        SQL);
    }
}
