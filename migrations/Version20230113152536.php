<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230113152536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE episode CHANGE season_id season_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE external_rating CHANGE series_id series_id INT DEFAULT NULL, CHANGE source_id source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rating CHANGE series_id series_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE comment comment LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE season CHANGE series_id series_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_3A10012D85489131 ON series');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user CHANGE `admin` `admin` TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user ADD ban TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE rating ADD valide TINYINT(1) NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE user CHANGE `admin` `admin` TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE episode CHANGE season_id season_id INT NOT NULL');
        $this->addSql('ALTER TABLE external_rating CHANGE source_id source_id INT NOT NULL, CHANGE series_id series_id INT NOT NULL');
        $this->addSql('ALTER TABLE rating CHANGE user_id user_id INT NOT NULL, CHANGE series_id series_id INT NOT NULL, CHANGE comment comment LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE series_id series_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A10012D85489131 ON series (imdb)');
        $this->addSql('ALTER TABLE user DROP ban');
        $this->addSql('ALTER TABLE rating DROP valide');
    }
}
