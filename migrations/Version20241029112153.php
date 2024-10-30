<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029112153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cursus (id INT AUTO_INCREMENT NOT NULL, thema_id INT NOT NULL, name VARCHAR(255) NOT NULL, level VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_255A0C3D660E3D0 (thema_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, cursus_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, number INT NOT NULL, price DOUBLE PRECISION NOT NULL, video VARCHAR(255) DEFAULT NULL, texte LONGTEXT DEFAULT NULL, INDEX IDX_F87474F340AEF4B9 (cursus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C3D660E3D0 FOREIGN KEY (thema_id) REFERENCES thema (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F340AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C3D660E3D0');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F340AEF4B9');
        $this->addSql('DROP TABLE cursus');
        $this->addSql('DROP TABLE lesson');
    }
}
