<?php

namespace App\Entity;

use App\Repository\ModeleRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModeleRepository::class)]
class Modele
{
    #[Groups(['vehicule_data', 'intervention_data', 'facture_data'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['intervention_data', 'facture_data'])]
    #[ORM\ManyToOne(inversedBy: 'modeles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Marque $fk_marque = null;

    #[Groups(['vehicule_data', 'intervention_data', 'facture_data'])]
    #[ORM\Column(length: 100)]
    private ?string $modele = null;

    #[ORM\OneToMany(mappedBy: 'fk_modele', targetEntity: Vehicule::class, orphanRemoval: true)]
    private Collection $vehicules;

    public function __construct()
    {
        $this->vehicules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFkMarque(): ?Marque
    {
        return $this->fk_marque;
    }

    public function setFkMarque(?Marque $fk_marque): static
    {
        $this->fk_marque = $fk_marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

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
            $vehicule->setFkModele($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            // set the owning side to null (unless already changed)
            if ($vehicule->getFkModele() === $this) {
                $vehicule->setFkModele(null);
            }
        }

        return $this;
    }
}
