<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226222058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vlog ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE vlog ADD CONSTRAINT FK_1F6E918BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1F6E918BF675F31B ON vlog (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vlog DROP FOREIGN KEY FK_1F6E918BF675F31B');
        $this->addSql('DROP INDEX IDX_1F6E918BF675F31B ON vlog');
        $this->addSql('ALTER TABLE vlog DROP author_id');
    }
}
