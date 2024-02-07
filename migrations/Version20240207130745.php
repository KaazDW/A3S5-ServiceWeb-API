<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207130745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE magasin (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, zip BIGINT NOT NULL, ville VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, latitude NUMERIC(10, 0) NOT NULL, longitude NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, type LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE magasin');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE utilisateur');
    }
}
