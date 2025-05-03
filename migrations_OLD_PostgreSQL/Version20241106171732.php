<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106171732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partie_epreuve ADD partie_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC679E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92FAC679E075F7A4 ON partie_epreuve (partie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE partie_epreuve DROP CONSTRAINT FK_92FAC679E075F7A4');
        $this->addSql('DROP INDEX IDX_92FAC679E075F7A4');
        $this->addSql('ALTER TABLE partie_epreuve DROP partie_id');
    }
}
