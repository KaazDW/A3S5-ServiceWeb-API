<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307162337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, date_commande DATE NOT NULL, status INT NOT NULL, client_id_id INT DEFAULT NULL, magasin_id_id INT DEFAULT NULL, horaire_id INT DEFAULT NULL, INDEX IDX_6EEAA67DDC2902E0 (client_id_id), INDEX IDX_6EEAA67D2B2B9060 (magasin_id_id), INDEX IDX_6EEAA67D58C54515 (horaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE creneau_horaire (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, heure_debut TIME NOT NULL, heure_fin TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE details_commande (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, commande_id_id INT DEFAULT NULL, produit_id_id INT DEFAULT NULL, INDEX IDX_4BCD5F6462C4194 (commande_id_id), INDEX IDX_4BCD5F64FD8F9C3 (produit_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE magasin (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, zip BIGINT NOT NULL, ville VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, latitude NUMERIC(10, 0) NOT NULL, longitude NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(255) NOT NULL, expediteur_id INT DEFAULT NULL, destinataire_id INT DEFAULT NULL, INDEX IDX_B6BD307F10335F61 (expediteur_id), INDEX IDX_B6BD307FA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE produit_magasin (produit_id INT NOT NULL, magasin_id INT NOT NULL, INDEX IDX_9254D45EF347EFB (produit_id), INDEX IDX_9254D45E20096AE3 (magasin_id), PRIMARY KEY(produit_id, magasin_id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, produit_id_id INT DEFAULT NULL, magasin_id_id INT DEFAULT NULL, INDEX IDX_4B3656604FD8F9C3 (produit_id_id), INDEX IDX_4B3656602B2B9060 (magasin_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DDC2902E0 FOREIGN KEY (client_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D2B2B9060 FOREIGN KEY (magasin_id_id) REFERENCES magasin (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D58C54515 FOREIGN KEY (horaire_id) REFERENCES creneau_horaire (id)');
        $this->addSql('ALTER TABLE details_commande ADD CONSTRAINT FK_4BCD5F6462C4194 FOREIGN KEY (commande_id_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE details_commande ADD CONSTRAINT FK_4BCD5F64FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE produit_magasin ADD CONSTRAINT FK_9254D45EF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_magasin ADD CONSTRAINT FK_9254D45E20096AE3 FOREIGN KEY (magasin_id) REFERENCES magasin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656602B2B9060 FOREIGN KEY (magasin_id_id) REFERENCES magasin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DDC2902E0');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D2B2B9060');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D58C54515');
        $this->addSql('ALTER TABLE details_commande DROP FOREIGN KEY FK_4BCD5F6462C4194');
        $this->addSql('ALTER TABLE details_commande DROP FOREIGN KEY FK_4BCD5F64FD8F9C3');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E');
        $this->addSql('ALTER TABLE produit_magasin DROP FOREIGN KEY FK_9254D45EF347EFB');
        $this->addSql('ALTER TABLE produit_magasin DROP FOREIGN KEY FK_9254D45E20096AE3');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604FD8F9C3');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656602B2B9060');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE creneau_horaire');
        $this->addSql('DROP TABLE details_commande');
        $this->addSql('DROP TABLE magasin');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE produit_magasin');
        $this->addSql('DROP TABLE stock');
        $this->addSql('DROP TABLE utilisateur');
    }
}
