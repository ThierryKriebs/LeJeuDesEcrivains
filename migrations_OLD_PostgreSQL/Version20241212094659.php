<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241212094659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE notation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notation (id INT NOT NULL, redaction_id INT NOT NULL, noteur_id INT NOT NULL, note INT NOT NULL, remarque TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_268BC953110A3EF ON notation (redaction_id)');
        $this->addSql('CREATE INDEX IDX_268BC9552798DD4 ON notation (noteur_id)');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC953110A3EF FOREIGN KEY (redaction_id) REFERENCES redaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC9552798DD4 FOREIGN KEY (noteur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE notation_id_seq CASCADE');
        $this->addSql('ALTER TABLE notation DROP CONSTRAINT FK_268BC953110A3EF');
        $this->addSql('ALTER TABLE notation DROP CONSTRAINT FK_268BC9552798DD4');
        $this->addSql('DROP TABLE notation');
    }
}
