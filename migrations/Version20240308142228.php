<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240308142228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notif (id INT AUTO_INCREMENT NOT NULL, contenu VARCHAR(255) NOT NULL, client_id_id INT DEFAULT NULL, commande_id_id INT DEFAULT NULL, INDEX IDX_C0730D6BDC2902E0 (client_id_id), INDEX IDX_C0730D6B462C4194 (commande_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6BDC2902E0 FOREIGN KEY (client_id_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6B462C4194 FOREIGN KEY (commande_id_id) REFERENCES commande (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notif DROP FOREIGN KEY FK_C0730D6BDC2902E0');
        $this->addSql('ALTER TABLE notif DROP FOREIGN KEY FK_C0730D6B462C4194');
        $this->addSql('DROP TABLE notif');
    }
}
