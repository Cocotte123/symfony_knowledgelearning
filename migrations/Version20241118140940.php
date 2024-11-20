<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241118140940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_cursus_lesson (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, cursus_id INT NOT NULL, learning_id INT NOT NULL, is_validated TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'Cart\' NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_by VARCHAR(255) DEFAULT NULL, INDEX IDX_51DA22E8A76ED395 (user_id), INDEX IDX_51DA22E840AEF4B9 (cursus_id), INDEX IDX_51DA22E84E6B0AB3 (learning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_cursus_lesson ADD CONSTRAINT FK_51DA22E8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_cursus_lesson ADD CONSTRAINT FK_51DA22E840AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE user_cursus_lesson ADD CONSTRAINT FK_51DA22E84E6B0AB3 FOREIGN KEY (learning_id) REFERENCES lesson (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_cursus_lesson DROP FOREIGN KEY FK_51DA22E8A76ED395');
        $this->addSql('ALTER TABLE user_cursus_lesson DROP FOREIGN KEY FK_51DA22E840AEF4B9');
        $this->addSql('ALTER TABLE user_cursus_lesson DROP FOREIGN KEY FK_51DA22E84E6B0AB3');
        $this->addSql('DROP TABLE user_cursus_lesson');
    }
}
