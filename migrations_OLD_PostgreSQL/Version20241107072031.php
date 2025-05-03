<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107072031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE epreuve_etat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE epreuve_etat (id INT NOT NULL, nom VARCHAR(100) NOT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE partie_epreuve ADD etat_epreuve_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC6792D53F23 FOREIGN KEY (etat_epreuve_id) REFERENCES epreuve_etat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92FAC6792D53F23 ON partie_epreuve (etat_epreuve_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE partie_epreuve DROP CONSTRAINT FK_92FAC6792D53F23');
        $this->addSql('DROP SEQUENCE epreuve_etat_id_seq CASCADE');
        $this->addSql('DROP TABLE epreuve_etat');
        $this->addSql('DROP INDEX IDX_92FAC6792D53F23');
        $this->addSql('ALTER TABLE partie_epreuve DROP etat_epreuve_id');
    }
}
