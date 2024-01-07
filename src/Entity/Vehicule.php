<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[Groups(['intervention_data'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $fk_client = null;

    #[Groups(['intervention_data'])]
    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Modele $fk_modele = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Carburant $fk_carburant = null;

    #[Groups(['intervention_data'])]
    #[ORM\Column(length: 10)]
    private ?string $immatriculation = null;

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $kilometrage = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etat $fk_etat = null;

    #[ORM\OneToMany(mappedBy: 'fk_vehicule', targetEntity: Intervention::class, orphanRemoval: true)]
    private Collection $interventions;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkClient(): ?Client
    {
        return $this->fk_client;
    }

    public function setFkClient(?Client $fk_client): static
    {
        $this->fk_client = $fk_client;

        return $this;
    }

    public function getFkModele(): ?Modele
    {
        return $this->fk_modele;
    }

    public function setFkModele(?Modele $fk_modele): static
    {
        $this->fk_modele = $fk_modele;

        return $this;
    }

    public function getFkCarburant(): ?Carburant
    {
        return $this->fk_carburant;
    }

    public function setFkCarburant(?Carburant $fk_carburant): static
    {
        $this->fk_carburant = $fk_carburant;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getKilometrage(): ?string
    {
        return $this->kilometrage;
    }

    public function setKilometrage(string $kilometrage): static
    {
        $this->kilometrage = $kilometrage;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

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
            $intervention->setFkVehicule($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            // set the owning side to null (unless already changed)
            if ($intervention->getFkVehicule() === $this) {
                $intervention->setFkVehicule(null);
            }
        }

        return $this;
    }
}
