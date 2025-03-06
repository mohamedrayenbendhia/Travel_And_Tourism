<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305162829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evaluation_reclamation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, reclamation_id INT NOT NULL, note VARCHAR(255) NOT NULL, commentaire LONGTEXT NOT NULL, INDEX IDX_9D39F073A76ED395 (user_id), INDEX IDX_9D39F0732D6BA2D9 (reclamation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, date_created DATETIME NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, auteur_id INT NOT NULL, cible_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, photo VARCHAR(255) DEFAULT NULL, document VARCHAR(255) DEFAULT NULL, statut VARCHAR(20) NOT NULL, date_soumission DATETIME NOT NULL, type_cible VARCHAR(50) NOT NULL, INDEX IDX_CE60640460BB6FE6 (auteur_id), INDEX IDX_CE606404A96E5E09 (cible_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vlog (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_1F6E918BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation_reclamation ADD CONSTRAINT FK_9D39F073A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE evaluation_reclamation ADD CONSTRAINT FK_9D39F0732D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE60640460BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404A96E5E09 FOREIGN KEY (cible_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vlog ADD CONSTRAINT FK_1F6E918BF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evaluation_reclamation DROP FOREIGN KEY FK_9D39F073A76ED395');
        $this->addSql('ALTER TABLE evaluation_reclamation DROP FOREIGN KEY FK_9D39F0732D6BA2D9');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE60640460BB6FE6');
        $this->addSql('ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404A96E5E09');
        $this->addSql('ALTER TABLE vlog DROP FOREIGN KEY FK_1F6E918BF675F31B');
        $this->addSql('DROP TABLE evaluation_reclamation');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vlog');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
