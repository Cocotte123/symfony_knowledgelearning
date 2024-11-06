<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106152238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cart_id VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'Cart\' NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orderdetail (id INT AUTO_INCREMENT NOT NULL, ordernumber_id INT NOT NULL, repository VARCHAR(255) NOT NULL, learning_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'Cart\' NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_27A0E7F2FF9979C1 (ordernumber_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE usercursus (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, repository VARCHAR(255) NOT NULL, learning_id INT NOT NULL, is_validated TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'Cart\' NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_6AAFB707A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orderdetail ADD CONSTRAINT FK_27A0E7F2FF9979C1 FOREIGN KEY (ordernumber_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE usercursus ADD CONSTRAINT FK_6AAFB707A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE orderdetail DROP FOREIGN KEY FK_27A0E7F2FF9979C1');
        $this->addSql('ALTER TABLE usercursus DROP FOREIGN KEY FK_6AAFB707A76ED395');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE orderdetail');
        $this->addSql('DROP TABLE usercursus');
    }
}
