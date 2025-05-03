<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114113316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie_etape (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, explication LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE epreuve_etat (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, commentaire LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre_litteraire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(150) NOT NULL, commentaire LONGTEXT DEFAULT NULL, exemple LONGTEXT DEFAULT NULL, est_active TINYINT(1) NOT NULL, nom_image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE longueur_partie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, nombre_etape INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notation (id INT AUTO_INCREMENT NOT NULL, redaction_id INT NOT NULL, noteur_id INT NOT NULL, note INT NOT NULL, remarque LONGTEXT DEFAULT NULL, INDEX IDX_268BC953110A3EF (redaction_id), INDEX IDX_268BC9552798DD4 (noteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie (id INT AUTO_INCREMENT NOT NULL, longueur_partie_id INT NOT NULL, genre_litteraire_id INT NOT NULL, etat_id INT NOT NULL, code_connexion VARCHAR(50) NOT NULL, date_creation DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_59B1F3DC066BC65 (longueur_partie_id), INDEX IDX_59B1F3DEE101E98 (genre_litteraire_id), INDEX IDX_59B1F3DD5E86FF (etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie_epreuve (id INT AUTO_INCREMENT NOT NULL, sous_categorie_id INT NOT NULL, partie_id INT NOT NULL, etat_epreuve_id INT NOT NULL, num_etape INT NOT NULL, date_debut_epreuve DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_92FAC679365BF48 (sous_categorie_id), INDEX IDX_92FAC679E075F7A4 (partie_id), INDEX IDX_92FAC6792D53F23 (etat_epreuve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie_etat (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, commentaire LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partie_joueur (id INT AUTO_INCREMENT NOT NULL, partie_id INT NOT NULL, joueur_id INT NOT NULL, est_createur TINYINT(1) NOT NULL, score DOUBLE PRECISION DEFAULT NULL, classement INT DEFAULT NULL, INDEX IDX_AE7EDCA9E075F7A4 (partie_id), INDEX IDX_AE7EDCA9A9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE redaction (id INT AUTO_INCREMENT NOT NULL, partie_epreuve_id INT NOT NULL, joueur_id INT NOT NULL, redaction LONGTEXT DEFAULT NULL, score DOUBLE PRECISION DEFAULT NULL, classement INT DEFAULT NULL, INDEX IDX_29A014E5FDA9FDB7 (partie_epreuve_id), INDEX IDX_29A014E5A9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sous_categorie_etape (id INT AUTO_INCREMENT NOT NULL, categorie_etape_id INT NOT NULL, nom VARCHAR(100) NOT NULL, duree_par_defaut INT NOT NULL, explication LONGTEXT NOT NULL, INDEX IDX_1156D561720BE3C7 (categorie_etape_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateurs (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_LOGIN (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC953110A3EF FOREIGN KEY (redaction_id) REFERENCES redaction (id)');
        $this->addSql('ALTER TABLE notation ADD CONSTRAINT FK_268BC9552798DD4 FOREIGN KEY (noteur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DC066BC65 FOREIGN KEY (longueur_partie_id) REFERENCES longueur_partie (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DEE101E98 FOREIGN KEY (genre_litteraire_id) REFERENCES genre_litteraire (id)');
        $this->addSql('ALTER TABLE partie ADD CONSTRAINT FK_59B1F3DD5E86FF FOREIGN KEY (etat_id) REFERENCES partie_etat (id)');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC679365BF48 FOREIGN KEY (sous_categorie_id) REFERENCES sous_categorie_etape (id)');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC679E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE partie_epreuve ADD CONSTRAINT FK_92FAC6792D53F23 FOREIGN KEY (etat_epreuve_id) REFERENCES epreuve_etat (id)');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA9E075F7A4 FOREIGN KEY (partie_id) REFERENCES partie (id)');
        $this->addSql('ALTER TABLE partie_joueur ADD CONSTRAINT FK_AE7EDCA9A9E2D76C FOREIGN KEY (joueur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT FK_29A014E5FDA9FDB7 FOREIGN KEY (partie_epreuve_id) REFERENCES partie_epreuve (id)');
        $this->addSql('ALTER TABLE redaction ADD CONSTRAINT FK_29A014E5A9E2D76C FOREIGN KEY (joueur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE sous_categorie_etape ADD CONSTRAINT FK_1156D561720BE3C7 FOREIGN KEY (categorie_etape_id) REFERENCES categorie_etape (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC953110A3EF');
        $this->addSql('ALTER TABLE notation DROP FOREIGN KEY FK_268BC9552798DD4');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DC066BC65');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DEE101E98');
        $this->addSql('ALTER TABLE partie DROP FOREIGN KEY FK_59B1F3DD5E86FF');
        $this->addSql('ALTER TABLE partie_epreuve DROP FOREIGN KEY FK_92FAC679365BF48');
        $this->addSql('ALTER TABLE partie_epreuve DROP FOREIGN KEY FK_92FAC679E075F7A4');
        $this->addSql('ALTER TABLE partie_epreuve DROP FOREIGN KEY FK_92FAC6792D53F23');
        $this->addSql('ALTER TABLE partie_joueur DROP FOREIGN KEY FK_AE7EDCA9E075F7A4');
        $this->addSql('ALTER TABLE partie_joueur DROP FOREIGN KEY FK_AE7EDCA9A9E2D76C');
        $this->addSql('ALTER TABLE redaction DROP FOREIGN KEY FK_29A014E5FDA9FDB7');
        $this->addSql('ALTER TABLE redaction DROP FOREIGN KEY FK_29A014E5A9E2D76C');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE sous_categorie_etape DROP FOREIGN KEY FK_1156D561720BE3C7');
        $this->addSql('DROP TABLE categorie_etape');
        $this->addSql('DROP TABLE epreuve_etat');
        $this->addSql('DROP TABLE genre_litteraire');
        $this->addSql('DROP TABLE longueur_partie');
        $this->addSql('DROP TABLE notation');
        $this->addSql('DROP TABLE partie');
        $this->addSql('DROP TABLE partie_epreuve');
        $this->addSql('DROP TABLE partie_etat');
        $this->addSql('DROP TABLE partie_joueur');
        $this->addSql('DROP TABLE redaction');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE sous_categorie_etape');
        $this->addSql('DROP TABLE utilisateurs');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
