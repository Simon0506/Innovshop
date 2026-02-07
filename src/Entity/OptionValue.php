<?php

namespace App\Entity;

use App\Repository\OptionValueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionValueRepository::class)]
class OptionValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'optionValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OptionGroup $optionGroup = null;

    /**
     * @var Collection<int, ProductVariantOption>
     */
    #[ORM\OneToMany(targetEntity: ProductVariantOption::class, mappedBy: 'optionValue', orphanRemoval: true, cascade: ['persist'])]
    private Collection $productVariantOptions;

    public function __construct()
    {
        $this->productVariantOptions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->getOptionGroup()->getName(), $this->getValue());
    }

    public function getName(): string
    {
        return $this->__toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getOptionGroup(): ?OptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(?OptionGroup $optionGroup): static
    {
        $this->optionGroup = $optionGroup;

        return $this;
    }

    /**
     * @return Collection<int, ProductVariantOption>
     */
    public function getProductVariantOptions(): Collection
    {
        return $this->productVariantOptions;
    }

    public function addProductVariantOption(ProductVariantOption $productVariantOption): static
    {
        if (!$this->productVariantOptions->contains($productVariantOption)) {
            $this->productVariantOptions->add($productVariantOption);
            $productVariantOption->setOptionValue($this);
        }

        return $this;
    }

    public function removeProductVariantOption(ProductVariantOption $productVariantOption): static
    {
        if ($this->productVariantOptions->removeElement($productVariantOption)) {
            // set the owning side to null (unless already changed)
            if ($productVariantOption->getOptionValue() === $this) {
                $productVariantOption->setOptionValue(null);
            }
        }

        return $this;
    }
}
