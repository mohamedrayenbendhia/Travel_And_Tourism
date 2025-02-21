<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220194210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, date_commande DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, restaurant_id_id INT DEFAULT NULL, commande_id INT DEFAULT NULL, plat VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_7D053A9335592D86 (restaurant_id_id), INDEX IDX_7D053A9382EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_transport (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, transport_id_id INT NOT NULL, date_reservation DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_7CEC40B19D86650F (user_id_id), UNIQUE INDEX UNIQ_7CEC40B1E24B39AF (transport_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transport (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, desription VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilte TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, commande_id INT DEFAULT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, INDEX IDX_8D93D64982EA2E54 (commande_id), UNIQUE INDEX UNIQ_IDENTIFIER_ID (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9335592D86 FOREIGN KEY (restaurant_id_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9382EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE reservation_transport ADD CONSTRAINT FK_7CEC40B19D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation_transport ADD CONSTRAINT FK_7CEC40B1E24B39AF FOREIGN KEY (transport_id_id) REFERENCES transport (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D64982EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9335592D86');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A9382EA2E54');
        $this->addSql('ALTER TABLE reservation_transport DROP FOREIGN KEY FK_7CEC40B19D86650F');
        $this->addSql('ALTER TABLE reservation_transport DROP FOREIGN KEY FK_7CEC40B1E24B39AF');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64982EA2E54');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE reservation_transport');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE transport');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
