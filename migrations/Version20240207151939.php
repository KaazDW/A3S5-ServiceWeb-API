<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207151939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, produit_id_id INT DEFAULT NULL, magasin_id_id INT DEFAULT NULL, INDEX IDX_4B3656604FD8F9C3 (produit_id_id), INDEX IDX_4B3656602B2B9060 (magasin_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656602B2B9060 FOREIGN KEY (magasin_id_id) REFERENCES magasin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604FD8F9C3');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656602B2B9060');
        $this->addSql('DROP TABLE stock');
    }
}
