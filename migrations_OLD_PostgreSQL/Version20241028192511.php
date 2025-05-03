<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241028192511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categorie_etape_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE genre_litteraire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE longueur_partie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE partie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE partie_epreuve_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE partie_etat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE partie_joueur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sous_categorie_etape_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateurs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categorie_etape (id INT NOT NULL, nom VARCHAR(100) NOT NULL, explication TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE genre_litteraire (id INT NOT NULL, nom VARCHAR(150) NOT NULL, commentaire TEXT DEFAULT NULL, exemple TEXT DEFAULT NULL, est_active BOOLEAN NOT NULL, nom_image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE longueur_partie (id INT NOT NULL, nom VARCHAR(100) NOT NULL, nombre_etape INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE partie (id INT NOT NULL, longueur_partie_id INT NOT NULL, genre_litteraire_id INT NOT NULL, etat_id INT NOT NULL, code_connexion VARCHAR(50) NOT NULL, date_creation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59B1F3DC066BC65 ON partie (longueur_partie_id)');
        $this->addSql('CREATE INDEX IDX_59B1F3DEE101E98 ON partie (genre_litteraire_id)');
        $this->addSql('CREATE INDEX IDX_59B1F3DD5E86FF ON partie (etat_id)');
        $this->addSql('COMMENT ON COLUMN partie.date_creation IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE partie_epreuve (id INT NOT NULL, id_sous_categorie_id INT NOT NULL, num_etape INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_92FAC679F252D75F ON partie_epreuve (id_sous_categorie_id)');
        $this->addSql('CREATE TABLE partie_etat (id INT NOT NULL, nom VARCHAR(100) NOT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE partie_joueur (id INT NOT NULL, id_partie_id INT NOT NULL, id_joueur_id INT NOT NULL, est_createur BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AE7EDCA960404B83 ON partie_joueur (id_partie_id)');
        $this->addSql('CREATE INDEX IDX_AE7EDCA929D76B4B ON partie_joueur (id_joueur_id)');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE sous_categorie_etape (id INT NOT NULL, categorie_etape_id INT NOT NULL, nom VARCHAR(100) NOT NULL, duree_par_defaut INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1156D561720BE3C7 ON sous_categorie_etape (categorie_etape_id)');
        $this->addSql('CREATE TABLE utilisateurs (id INT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN ON utilisateurs (login)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DC066BC65 FOREIGN KEY (longueur_partie_id) REFERENCES longueur_partie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DEE101E98 FOREIGN KEY (genre_litteraire_id) REFERENCES genre_litteraire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DD5E86FF FOREIGN KEY (etat_id) REFERENCES partie_etat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC679F252D75F FOREIGN KEY (id_sous_categorie_id) REFERENCES sous_categorie_etape (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA960404B83 FOREIGN KEY (id_partie_id) REFERENCES partie (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA929D76B4B FOREIGN KEY (id_joueur_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sous_categorie_etape ADD CONSTRAINT FK_1156D561720BE3C7 FOREIGN KEY (categorie_etape_id) REFERENCES categorie_etape (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE categorie_etape_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE genre_litteraire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE longueur_partie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE partie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE partie_epreuve_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE partie_etat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE partie_joueur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sous_categorie_etape_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateurs_id_seq CASCADE');
        $this->addSql('ALTER TABLE partie DROP CONSTRAINT FK_59B1F3DC066BC65');
        $this->addSql('ALTER TABLE partie DROP CONSTRAINT FK_59B1F3DEE101E98');
        $this->addSql('ALTER TABLE partie DROP CONSTRAINT FK_59B1F3DD5E86FF');
        $this->addSql('ALTER TABLE partie_epreuve DROP CONSTRAINT FK_92FAC679F252D75F');
        $this->addSql('ALTER TABLE partie_joueur DROP CONSTRAINT FK_AE7EDCA960404B83');
        $this->addSql('ALTER TABLE partie_joueur DROP CONSTRAINT FK_AE7EDCA929D76B4B');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE sous_categorie_etape DROP CONSTRAINT FK_1156D561720BE3C7');
        $this->addSql('DROP TABLE categorie_etape');
        $this->addSql('DROP TABLE genre_litteraire');
        $this->addSql('DROP TABLE longueur_partie');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE partie_epreuve');
        $this->addSql('DROP TABLE partie_etat');
        $this->addSql('DROP TABLE partie_joueur');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE sous_categorie_etape');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
