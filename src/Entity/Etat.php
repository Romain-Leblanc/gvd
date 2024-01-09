<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[Groups(['facture_data'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['facture_data'])]
    #[ORM\ManyToOne(inversedBy: 'etats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeEtat $fk_type_etat = null;

    #[Groups(['facture_data'])]
    #[ORM\Column(length: 30)]
    private ?string $etat = null;

    #[ORM\OneToMany(mappedBy: 'fk_etat', targetEntity: Vehicule::class, orphanRemoval: true)]
    private Collection $vehicules;

    #[ORM\OneToMany(mappedBy: 'fk_etat', targetEntity: Intervention::class, orphanRemoval: true)]
    private Collection $interventions;

    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
        $this->interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkTypeEtat(): ?TypeEtat
    {
        return $this->fk_type_etat;
    }

    public function setFkTypeEtat(?TypeEtat $fk_type_etat): static
    {
        $this->fk_type_etat = $fk_type_etat;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Vehicule>
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
            $vehicule->setFkEtat($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            // set the owning side to null (unless already changed)
            if ($vehicule->getFkEtat() === $this) {
                $vehicule->setFkEtat(null);
            }
        }

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
            $intervention->setFkEtat($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            // set the owning side to null (unless already changed)
            if ($intervention->getFkEtat() === $this) {
                $intervention->setFkEtat(null);
            }
        }

        return $this;
    }
}
