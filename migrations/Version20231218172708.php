<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218172708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etat (id INT AUTO_INCREMENT NOT NULL, fk_type_etat_id INT NOT NULL, etat VARCHAR(30) NOT NULL, INDEX IDX_55CAF7624A2EB13 (fk_type_etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modele (id INT AUTO_INCREMENT NOT NULL, fk_marque_id INT NOT NULL, modele VARCHAR(100) NOT NULL, INDEX IDX_10028558297E6E22 (fk_marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, fk_client_id INT NOT NULL, fk_modele_id INT NOT NULL, fk_carburant_id INT NOT NULL, fk_etat_id INT NOT NULL, immatriculation VARCHAR(10) NOT NULL, kilometrage BIGINT NOT NULL, annee INT NOT NULL, INDEX IDX_292FFF1D78B2BEB1 (fk_client_id), INDEX IDX_292FFF1DCD4D609A (fk_modele_id), INDEX IDX_292FFF1D1307AF3D (fk_carburant_id), INDEX IDX_292FFF1DFD71BBD3 (fk_etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etat ADD CONSTRAINT FK_55CAF7624A2EB13 FOREIGN KEY (fk_type_etat_id) REFERENCES type_etat (id)');
        $this->addSql('ALTER TABLE modele ADD CONSTRAINT FK_10028558297E6E22 FOREIGN KEY (fk_marque_id) REFERENCES marque (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D78B2BEB1 FOREIGN KEY (fk_client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DCD4D609A FOREIGN KEY (fk_modele_id) REFERENCES modele (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1D1307AF3D FOREIGN KEY (fk_carburant_id) REFERENCES carburant (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DFD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP FOREIGN KEY FK_55CAF7624A2EB13');
        $this->addSql('ALTER TABLE modele DROP FOREIGN KEY FK_10028558297E6E22');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D78B2BEB1');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DCD4D609A');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1D1307AF3D');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DFD71BBD3');
        $this->addSql('DROP TABLE etat');
        $this->addSql('DROP TABLE modele');
        $this->addSql('DROP TABLE vehicule');
    }
}
