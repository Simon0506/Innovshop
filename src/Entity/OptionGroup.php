<?php

namespace App\Entity;

use App\Repository\OptionGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: OptionGroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
class OptionGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, OptionValue>
     */
    #[ORM\OneToMany(targetEntity: OptionValue::class, mappedBy: 'optionGroup', orphanRemoval: true, cascade: ['persist'])]
    private Collection $optionValues;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateSlug(): void
    {
        if (!$this->slug && $this->name) {
            $slugger = new AsciiSlugger();
            $this->slug = strtolower($slugger->slug($this->name)->toString());
        }
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->optionValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, OptionValue>
     */
    public function getOptionValues(): Collection
    {
        return $this->optionValues;
    }

    public function addOptionValue(OptionValue $optionValue): static
    {
        if (!$this->optionValues->contains($optionValue)) {
            $this->optionValues->add($optionValue);
            $optionValue->setOptionGroup($this);
        }

        return $this;
    }

    public function removeOptionValue(OptionValue $optionValue): static
    {
        if ($this->optionValues->removeElement($optionValue)) {
            // set the owning side to null (unless already changed)
            if ($optionValue->getOptionGroup() === $this) {
                $optionValue->setOptionGroup(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
