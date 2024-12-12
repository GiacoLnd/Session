<?php

namespace App\Entity;

use App\Repository\FormModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormModuleRepository::class)]
class FormModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $moduleName = null;

    #[ORM\ManyToOne(inversedBy: 'FormModules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $categorie = null;

    /**
     * @var Collection<int, Programme>
     */
    #[ORM\OneToMany(targetEntity: Programme::class, mappedBy: 'FormModules', orphanRemoval: true)]
    private Collection $programmes;

    public function __construct()
    {
        $this->programmes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function setModuleName(string $moduleName): static
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    public function getCategorie(): ?Category
    {
        return $this->categorie;
    }

    public function setCategorie(?Category $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Programme>
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programme $programme): static
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes->add($programme);
            $programme->setFormModules($this);
        }

        return $this;
    }

    public function removeProgramme(Programme $programme): static
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getFormModules() === $this) {
                $programme->setFormModules(null);
            }
        }

        return $this;
    }
}
