<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212093207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE redaction DROP CONSTRAINT fk_29a014e552798dd4');
        $this->addSql('DROP INDEX idx_29a014e552798dd4');
        $this->addSql('ALTER TABLE redaction DROP noteur_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE redaction ADD noteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT fk_29a014e552798dd4 FOREIGN KEY (noteur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_29a014e552798dd4 ON redaction (noteur_id)');
    }
}
