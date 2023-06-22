<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622142347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, reporter_id INT NOT NULL, reported_post_id INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status INT DEFAULT 1 NOT NULL, INDEX IDX_F11FA745E1CFE6F5 (reporter_id), INDEX IDX_F11FA745EC0086D7 (reported_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745EC0086D7 FOREIGN KEY (reported_post_id) REFERENCES post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745E1CFE6F5');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745EC0086D7');
        $this->addSql('DROP TABLE reports');
    }
}
