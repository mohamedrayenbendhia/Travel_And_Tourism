<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226144911 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vlog ADD content LONGTEXT NOT NULL, ADD images JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD videos JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', ADD created_at DATETIME NOT NULL, DROP description, DROP title, DROP image, DROP video');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vlog ADD description LONGTEXT DEFAULT NULL, ADD title VARCHAR(255) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL, ADD video VARCHAR(255) DEFAULT NULL, DROP content, DROP images, DROP videos, DROP created_at');
    }
}
