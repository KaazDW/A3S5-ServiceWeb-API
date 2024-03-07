<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307165413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(255) NOT NULL, expediteur_id INT DEFAULT NULL, destinataire_id INT DEFAULT NULL, INDEX IDX_B6BD307F10335F61 (expediteur_id), INDEX IDX_B6BD307FA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES utilisateur (id)');
        $this->addSql('DROP INDEX IDX_6EEAA67D58C54515 ON commande');
        $this->addSql('ALTER TABLE commande CHANGE horaire_id creneau_horaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DDC2902E0 FOREIGN KEY (client_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D2B2B9060 FOREIGN KEY (magasin_id_id) REFERENCES magasin (id)');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D1709936 FOREIGN KEY (creneau_horaire_id) REFERENCES creneau_horaire (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D1709936 ON commande (creneau_horaire_id)');
        $this->addSql('ALTER TABLE creneau_horaire ADD heure TIME NOT NULL, DROP heure_debut, DROP heure_fin');
        $this->addSql('ALTER TABLE details_commande ADD CONSTRAINT FK_4BCD5F6462C4194 FOREIGN KEY (commande_id_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE details_commande ADD CONSTRAINT FK_4BCD5F64FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_magasin ADD CONSTRAINT FK_9254D45EF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_magasin ADD CONSTRAINT FK_9254D45E20096AE3 FOREIGN KEY (magasin_id) REFERENCES magasin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656602B2B9060 FOREIGN KEY (magasin_id_id) REFERENCES magasin (id)');
        $this->addSql('ALTER TABLE utilisateur ADD roles JSON NOT NULL, DROP type');
        $this->addSql('ALTER TABLE creneau_horaire ADD heure TIME NOT NULL, DROP heure_debut, DROP heure_fin');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FA4F84F6E');
        $this->addSql('DROP TABLE message');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DDC2902E0');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D2B2B9060');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D1709936');
        $this->addSql('DROP INDEX IDX_6EEAA67D1709936 ON commande');
        $this->addSql('ALTER TABLE commande CHANGE creneau_horaire_id horaire_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_6EEAA67D58C54515 ON commande (horaire_id)');
        $this->addSql('ALTER TABLE creneau_horaire ADD heure_fin TIME NOT NULL, CHANGE heure heure_debut TIME NOT NULL');
        $this->addSql('ALTER TABLE details_commande DROP FOREIGN KEY FK_4BCD5F6462C4194');
        $this->addSql('ALTER TABLE details_commande DROP FOREIGN KEY FK_4BCD5F64FD8F9C3');
        $this->addSql('ALTER TABLE produit_magasin DROP FOREIGN KEY FK_9254D45EF347EFB');
        $this->addSql('ALTER TABLE produit_magasin DROP FOREIGN KEY FK_9254D45E20096AE3');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604FD8F9C3');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656602B2B9060');
        $this->addSql('ALTER TABLE utilisateur ADD type LONGTEXT NOT NULL, DROP roles');
    }
}
