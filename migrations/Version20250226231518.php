<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226231518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vlog (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, travel_date DATETIME NOT NULL, content LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, images JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', videos JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', category VARCHAR(255) NOT NULL, tags VARCHAR(255) DEFAULT NULL, is_public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_1F6E918BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vlog ADD CONSTRAINT FK_1F6E918BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vlog DROP FOREIGN KEY FK_1F6E918BF675F31B');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vlog');
    }
}
