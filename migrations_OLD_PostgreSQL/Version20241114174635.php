<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241114174635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE redaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE redaction (id INT NOT NULL, partie_epreuve_id INT NOT NULL, joueur_id INT NOT NULL, noteur_id INT DEFAULT NULL, redaction TEXT NOT NULL, score DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29A014E5FDA9FDB7 ON redaction (partie_epreuve_id)');
        $this->addSql('CREATE INDEX IDX_29A014E5A9E2D76C ON redaction (joueur_id)');
        $this->addSql('CREATE INDEX IDX_29A014E552798DD4 ON redaction (noteur_id)');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT FK_29A014E5FDA9FDB7 FOREIGN KEY (partie_epreuve_id) REFERENCES partie_epreuve (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT FK_29A014E5A9E2D76C FOREIGN KEY (joueur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT FK_29A014E552798DD4 FOREIGN KEY (noteur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE redaction_id_seq CASCADE');
        $this->addSql('ALTER TABLE redaction DROP CONSTRAINT FK_29A014E5FDA9FDB7');
        $this->addSql('ALTER TABLE redaction DROP CONSTRAINT FK_29A014E5A9E2D76C');
        $this->addSql('ALTER TABLE redaction DROP CONSTRAINT FK_29A014E552798DD4');
        $this->addSql('DROP TABLE redaction');
    }
}
