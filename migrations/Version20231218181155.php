<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218181155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, fk_taux_id INT NOT NULL, fk_moyen_paiement_id INT DEFAULT NULL, date_facture DATE NOT NULL, date_paiement DATE DEFAULT NULL, montant_ht DOUBLE PRECISION NOT NULL, montant_tva DOUBLE PRECISION NOT NULL, montant_ttc DOUBLE PRECISION NOT NULL, INDEX IDX_FE866410B075317B (fk_taux_id), INDEX IDX_FE8664105249AB64 (fk_moyen_paiement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervention (id INT AUTO_INCREMENT NOT NULL, fk_vehicule_id INT NOT NULL, fk_facture_id INT DEFAULT NULL, fk_etat_id INT NOT NULL, date_creation DATE NOT NULL, date_intervention DATE NOT NULL, duree SMALLINT NOT NULL, detail VARCHAR(500) NOT NULL, montant_ht DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D11814AB23BC9925 (fk_vehicule_id), INDEX IDX_D11814AB8F43249B (fk_facture_id), INDEX IDX_D11814ABFD71BBD3 (fk_etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410B075317B FOREIGN KEY (fk_taux_id) REFERENCES tva (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE8664105249AB64 FOREIGN KEY (fk_moyen_paiement_id) REFERENCES moyen_paiement (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB23BC9925 FOREIGN KEY (fk_vehicule_id) REFERENCES vehicule (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814AB8F43249B FOREIGN KEY (fk_facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE intervention ADD CONSTRAINT FK_D11814ABFD71BBD3 FOREIGN KEY (fk_etat_id) REFERENCES etat (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410B075317B');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE8664105249AB64');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB23BC9925');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814AB8F43249B');
        $this->addSql('ALTER TABLE intervention DROP FOREIGN KEY FK_D11814ABFD71BBD3');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE intervention');
    }
}
