<?php

namespace App\Entity;

use App\Repository\TypeEtatRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeEtatRepository::class)]
class TypeEtat
{
    #[Groups(['facture_data'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['facture_data'])]
    #[ORM\Column(length: 25)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'fk_type_etat', targetEntity: Etat::class, orphanRemoval: true)]
    private Collection $etats;

    public function __construct()
    {
        $this->etats = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Etat>
     */
    public function getEtats(): Collection
    {
        return $this->etats;
    }

    public function addEtat(Etat $etat): static
    {
        if (!$this->etats->contains($etat)) {
            $this->etats->add($etat);
            $etat->setFkTypeEtat($this);
        }

        return $this;
    }

    public function removeEtat(Etat $etat): static
    {
        if ($this->etats->removeElement($etat)) {
            // set the owning side to null (unless already changed)
            if ($etat->getFkTypeEtat() === $this) {
                $etat->setFkTypeEtat(null);
            }
        }

        return $this;
    }
}
