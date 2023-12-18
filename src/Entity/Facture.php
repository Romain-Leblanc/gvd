<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TVA $fk_taux = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?MoyenPaiement $fk_moyen_paiement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_facture = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_paiement = null;

    #[ORM\Column]
    private ?float $montant_ht = null;

    #[ORM\Column]
    private ?float $montant_tva = null;

    #[ORM\Column]
    private ?float $montant_ttc = null;

    #[ORM\OneToMany(mappedBy: 'fk_facture', targetEntity: Intervention::class)]
    private Collection $interventions;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkTaux(): ?TVA
    {
        return $this->fk_taux;
    }

    public function setFkTaux(?TVA $fk_taux): static
    {
        $this->fk_taux = $fk_taux;

        return $this;
    }

    public function getFkMoyenPaiement(): ?MoyenPaiement
    {
        return $this->fk_moyen_paiement;
    }

    public function setFkMoyenPaiement(?MoyenPaiement $fk_moyen_paiement): static
    {
        $this->fk_moyen_paiement = $fk_moyen_paiement;

        return $this;
    }

    public function getDateFacture(): ?\DateTimeInterface
    {
        return $this->date_facture;
    }

    public function setDateFacture(\DateTimeInterface $date_facture): static
    {
        $this->date_facture = $date_facture;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?\DateTimeInterface $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getMontantHt(): ?float
    {
        return $this->montant_ht;
    }

    public function setMontantHt(float $montant_ht): static
    {
        $this->montant_ht = $montant_ht;

        return $this;
    }

    public function getMontantTva(): ?float
    {
        return $this->montant_tva;
    }

    public function setMontantTva(float $montant_tva): static
    {
        $this->montant_tva = $montant_tva;

        return $this;
    }

    public function getMontantTtc(): ?float
    {
        return $this->montant_ttc;
    }

    public function setMontantTtc(float $montant_ttc): static
    {
        $this->montant_ttc = $montant_ttc;

        return $this;
    }

    /**
     * @return Collection<int, Intervention>
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): static
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions->add($intervention);
            $intervention->setFkFacture($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            // set the owning side to null (unless already changed)
            if ($intervention->getFkFacture() === $this) {
                $intervention->setFkFacture(null);
            }
        }

        return $this;
    }
}
