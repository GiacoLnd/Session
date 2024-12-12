<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $CategoryModuleName = null;

    /**
     * @var Collection<int, FormModule>
     */
    #[ORM\OneToMany(targetEntity: FormModule::class, mappedBy: 'categorie')]
    private Collection $formModules;

    public function __construct()
    {
        $this->formModules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryModuleName(): ?string
    {
        return $this->CategoryModuleName;
    }

    public function setCategoryModuleName(string $CategoryModuleName): static
    {
        $this->CategoryModuleName = $CategoryModuleName;

        return $this;
    }

    /**
     * @return Collection<int, FormModule>
     */
    public function getFormModules(): Collection
    {
        return $this->formModules;
    }

    public function addFormModule(FormModule $formModule): static
    {
        if (!$this->formModules->contains($formModule)) {
            $this->formModules->add($formModule);
            $formModule->setCategorie($this);
        }

        return $this;
    }

    public function removeFormModule(FormModule $formModule): static
    {
        if ($this->formModules->removeElement($formModule)) {
            // set the owning side to null (unless already changed)
            if ($formModule->getCategorie() === $this) {
                $formModule->setCategorie(null);
            }
        }

        return $this;
    }
}
