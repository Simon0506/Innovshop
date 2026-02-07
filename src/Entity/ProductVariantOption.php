<?php

namespace App\Entity;

use App\Repository\ProductVariantOptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Entity(repositoryClass: ProductVariantOptionRepository::class)]
#[ORM\Table(uniqueConstraints: [
    new UniqueConstraint(name: 'unique_variant_option', columns: ['product_variant_id', 'option_value_id'])
])]
class ProductVariantOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productVariantOptions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ProductVariant $productVariant = null;

    #[ORM\ManyToOne(inversedBy: 'productVariantOptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OptionValue $optionValue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductVariant(): ?ProductVariant
    {
        return $this->productVariant;
    }

    public function setProductVariant(?ProductVariant $productVariant): static
    {
        $this->productVariant = $productVariant;

        return $this;
    }

    public function getOptionValue(): ?OptionValue
    {
        return $this->optionValue;
    }

    public function setOptionValue(?OptionValue $optionValue): static
    {
        $this->optionValue = $optionValue;

        return $this;
    }
}
