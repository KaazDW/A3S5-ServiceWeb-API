<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207162332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD horaire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D58C54515 FOREIGN KEY (horaire_id) REFERENCES creneau_horaire (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67D58C54515 ON commande (horaire_id)');

        $this->addSql("INSERT INTO Commande (`date_commande`,`status`,`client_id_id`,`magasin_id_id`,`horaire_id`) VALUES ( '2024-02-02','0','7','1','1');");
        $this->addSql("INSERT INTO Commande (`date_commande`,`status`,`client_id_id`,`magasin_id_id`,`horaire_id`) VALUES ( '2024-02-02','0','7','1','1');");
        $this->addSql("INSERT INTO DetailsCommande (`quantite`,`commande_id_id`,`produit_id_id`) VALUES ( '2','1','5');");
        $this->addSql("INSERT INTO DetailsCommande (`quantite`,`commande_id_id`,`produit_id_id`) VALUES ( '3','1','5');");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D58C54515');
        $this->addSql('DROP INDEX IDX_6EEAA67D58C54515 ON commande');
        $this->addSql('ALTER TABLE commande DROP horaire_id');
    }
}
