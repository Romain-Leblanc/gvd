<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicule $fk_vehicule = null;

    #[ORM\ManyToOne(inversedBy: 'interventions')]
    private ?Facture $fk_facture = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_intervention = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $duree = null;

    #[ORM\Column(length: 500)]
    private ?string $detail = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant_ht = null;

    #[ORM\ManyToOne(inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $fk_etat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkVehicule(): ?Vehicule
    {
        return $this->fk_vehicule;
    }

    public function setFkVehicule(?Vehicule $fk_vehicule): static
    {
        $this->fk_vehicule = $fk_vehicule;

        return $this;
    }

    public function getFkFacture(): ?Facture
    {
        return $this->fk_facture;
    }

    public function setFkFacture(?Facture $fk_facture): static
    {
        $this->fk_facture = $fk_facture;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateIntervention(): ?\DateTimeInterface
    {
        return $this->date_intervention;
    }

    public function setDateIntervention(\DateTimeInterface $date_intervention): static
    {
        $this->date_intervention = $date_intervention;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): static
    {
        $this->detail = $detail;

        return $this;
    }

    public function getMontantHt(): ?float
    {
        return $this->montant_ht;
    }

    public function setMontantHt(?float $montant_ht): static
    {
        $this->montant_ht = $montant_ht;

        return $this;
    }

    public function getFkEtat(): ?Etat
    {
        return $this->fk_etat;
    }

    public function setFkEtat(?Etat $fk_etat): static
    {
        $this->fk_etat = $fk_etat;

        return $this;
    }
}
