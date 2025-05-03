<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028194018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partie_epreuve DROP CONSTRAINT fk_92fac679f252d75f');
        $this->addSql('DROP INDEX idx_92fac679f252d75f');
        $this->addSql('ALTER TABLE partie_epreuve RENAME COLUMN id_sous_categorie_id TO sous_categorie_id');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC679365BF48 FOREIGN KEY (sous_categorie_id) REFERENCES sous_categorie_etape (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_92FAC679365BF48 ON partie_epreuve (sous_categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE partie_epreuve DROP CONSTRAINT FK_92FAC679365BF48');
        $this->addSql('DROP INDEX IDX_92FAC679365BF48');
        $this->addSql('ALTER TABLE partie_epreuve RENAME COLUMN sous_categorie_id TO id_sous_categorie_id');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT fk_92fac679f252d75f FOREIGN KEY (id_sous_categorie_id) REFERENCES sous_categorie_etape (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_92fac679f252d75f ON partie_epreuve (id_sous_categorie_id)');
    }
}
