<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207152334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'insertion de données';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Magasin1','19 rue du chemin','01000','bourg','france','10.02','10.03');");
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Magasin2','boulevard ','93000','Paris','france','10.02','10.03');");
        $this->addSql("INSERT INTO Utilisateur ( `nom`,`prenom`,`email`,`password`,`type`) VALUES ( 'Pauline','trontin','test@gmail.com','test','1');");
        $this->addSql("INSERT INTO Utilisateur ( `nom`,`prenom`,`email`,`password`,`type`) VALUES ( 'JF','Marcourt','test@gmail.com','test','1');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Chaussure','chaussure','90.0');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Farine','farine de blé','2.3');");
        $this->addSql("INSERT INTO Stock (`quantite`,`produit_id_id`,`magasin_id_id`) VALUES ('30','4','1');");
        $this->addSql("INSERT INTO Stock (`quantite`,`produit_id_id`,`magasin_id_id`) VALUES ('20','5','1');");
    }

    public function down(Schema $schema): void
    {

    }
}
