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
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Magasin1','19 rue du chemin','01000','bourg','france','46.2','5.216667');");
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Super U','75 boulevard ','93000','Paris','france','48.866667','2.333333');");
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Leclerc','13 rondpoint  ','69000','Lyon','france','45.75','4.85');");
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Auchan','01 pont  ','26000','Valence','france','44.933393','4.89236');");
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Lidl','33 route du pont ','45380','Orleans','france','47.902964','1.909251');");
        $this->addSql("INSERT INTO Magasin (`nom`,`adresse`,`zip`,`ville`,`pays`,`latitude`,`longitude`) VALUES ( 'Carrefour','98 boulevard ','13000','Marseille','france','43.3','5.4');");
        $this->addSql("INSERT INTO Utilisateur ( `nom`,`prenom`,`email`,`password`,`type`) VALUES ( 'Pauline','trontin','test@gmail.com','test','1');");
        $this->addSql("INSERT INTO Utilisateur ( `nom`,`prenom`,`email`,`password`,`type`) VALUES ( 'JF','Marcourt','test@gmail.com','test','1');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Chaussure','chaussure à talon','90.0');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Veste','Veste lacoste','75.0');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Bonnet','Bonnet lacoste','10.0');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Sucre','Sucre de canne','1.8');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Farine','farine de blé','2.3');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Ferrari ','Ferrari','20000');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Lamborghini','Lamborghini','10000');");
        $this->addSql("INSERT INTO Produit (`nom`,`description`,`prix`) VALUES ('Vélo','Bicilette','59.99');");
        $this->addSql("INSERT INTO Stock (`quantite`,`produit_id_id`,`magasin_id_id`) VALUES ('30','1','1');");
        $this->addSql("INSERT INTO Stock (`quantite`,`produit_id_id`,`magasin_id_id`) VALUES ('20','2','1');");
        $this->addSql("INSERT INTO Stock (`quantite`,`produit_id_id`,`magasin_id_id`) VALUES ('10','3','2');");
        $this->addSql("INSERT INTO Stock (`quantite`,`produit_id_id`,`magasin_id_id`) VALUES ('13','4','2');");
    }

    public function down(Schema $schema): void
    {

    }
}
