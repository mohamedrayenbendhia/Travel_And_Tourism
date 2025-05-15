<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240317001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add ban functionality to User entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD is_banned TINYINT(1) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE user ADD ban_reason LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP is_banned');
        $this->addSql('ALTER TABLE user DROP ban_reason');
    }
} 