<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303202234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_transport DROP INDEX UNIQ_7CEC40B1E24B39AF, ADD INDEX IDX_7CEC40B1E24B39AF (transport_id_id)');
        $this->addSql('ALTER TABLE reservation_transport ADD end_date DATETIME NOT NULL, CHANGE date_reservation start_date DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_transport DROP INDEX IDX_7CEC40B1E24B39AF, ADD UNIQUE INDEX UNIQ_7CEC40B1E24B39AF (transport_id_id)');
        $this->addSql('ALTER TABLE reservation_transport ADD date_reservation DATETIME NOT NULL, DROP start_date, DROP end_date');
    }
}
