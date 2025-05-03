<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028195431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partie_joueur DROP CONSTRAINT fk_ae7edca960404b83');
        $this->addSql('ALTER TABLE partie_joueur DROP CONSTRAINT fk_ae7edca929d76b4b');
        $this->addSql('DROP INDEX idx_ae7edca929d76b4b');
        $this->addSql('DROP INDEX idx_ae7edca960404b83');
        $this->addSql('ALTER TABLE partie_joueur ADD partie_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie_joueur ADD joueur_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie_joueur DROP id_partie_id');
        $this->addSql('ALTER TABLE partie_joueur DROP id_joueur_id');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA9E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA9A9E2D76C FOREIGN KEY (joueur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AE7EDCA9E075F7A4 ON partie_joueur (partie_id)');
        $this->addSql('CREATE INDEX IDX_AE7EDCA9A9E2D76C ON partie_joueur (joueur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE partie_joueur DROP CONSTRAINT FK_AE7EDCA9E075F7A4');
        $this->addSql('ALTER TABLE partie_joueur DROP CONSTRAINT FK_AE7EDCA9A9E2D76C');
        $this->addSql('DROP INDEX IDX_AE7EDCA9E075F7A4');
        $this->addSql('DROP INDEX IDX_AE7EDCA9A9E2D76C');
        $this->addSql('ALTER TABLE partie_joueur ADD id_partie_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie_joueur ADD id_joueur_id INT NOT NULL');
        $this->addSql('ALTER TABLE partie_joueur DROP partie_id');
        $this->addSql('ALTER TABLE partie_joueur DROP joueur_id');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT fk_ae7edca960404b83 FOREIGN KEY (id_partie_id) REFERENCES partie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT fk_ae7edca929d76b4b FOREIGN KEY (id_joueur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ae7edca929d76b4b ON partie_joueur (id_joueur_id)');
        $this->addSql('CREATE INDEX idx_ae7edca960404b83 ON partie_joueur (id_partie_id)');
    }
}
