<?php

namespace App\Entity;

use App\Repository\ProductVariantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\UniqueOptionGroup;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
#[UniqueOptionGroup]
class ProductVariant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sku = null;

    #[ORM\ManyToOne(inversedBy: 'productVariants')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Products $product = null;

    /**
     * @var Collection<int, ProductVariantOption>
     */
    #[ORM\OneToMany(targetEntity: ProductVariantOption::class, mappedBy: 'productVariant', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $productVariantOptions;

    /**
     * @var OptionValue[]|null
     */
    private ?array $selectedOptionValues = null;

    /**
     * @var Collection<int, OrderLines>
     */
    #[ORM\OneToMany(targetEntity: OrderLines::class, mappedBy: 'productVariant', orphanRemoval: true)]
    private Collection $orderLines;

    #[ORM\Column]
    private ?float $tva = 20.0;

    public function __construct()
    {
        $this->productVariantOptions = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
    }

    public function __toString(): string
    {
        $options = [];
        foreach ($this->getProductVariantOptions() as $pvo) {
            $options[] = $pvo->getOptionValue()->getName();
        }
        if (empty($options)) {
            return $this->getProduct() ? $this->getProduct()->getTitle() : 'Variante sans options';
        }
        $optionsStr = implode(', ', $options);
        return sprintf('%s', $optionsStr);
    }

    private function sortOptionsByGroup(array $options): array
    {
        usort($options, function (ProductVariantOption $a, ProductVariantOption $b) {
            return $a->getOptionValue()->getOptionGroup()->getId() <=> $b->getOptionValue()->getOptionGroup()->getId();
        });
        return $options;
    }

    public function getName(): string
    {
        $options = $this->sortOptionsByGroup($this->getProductVariantOptions()->toArray());
        $optionNames = array_map(function (ProductVariantOption $pvo) {
            return $pvo->getOptionValue()->getValue();
        }, $options);

        return implode(' - ', $optionNames);
    }

    public function getTitle(): string
    {
        $options = $this->sortOptionsByGroup($this->getProductVariantOptions()->toArray());
        $optionName = implode(' ', array_map(function (ProductVariantOption $pvo) {
            return $pvo->getOptionValue()->getValue();
        }, $options));

        $productTitle = $this->getProduct() ? $this->getProduct()->getTitle() : '';

        return implode(' - ', [$productTitle, $optionName]);
    }

    public function getSlug(): ?string
    {
        return $this->getProduct() ? $this->getProduct()->getSlug() : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): static
    {
        $this->sku = $sku;

        return $this;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): static
    {
        $this->product = $product;

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
            $productVariantOption->setProductVariant($this);
        }

        return $this;
    }

    public function removeProductVariantOption(ProductVariantOption $productVariantOption): static
    {
        $this->productVariantOptions->removeElement($productVariantOption);

        return $this;
    }

    /**
     * @return OptionValue[]
     */
    public function getSelectedOptionValues(): array
    {
        if ($this->selectedOptionValues === null) {
            $this->selectedOptionValues = [];
            foreach ($this->getproductVariantOptions() as $pvo) {
                $this->selectedOptionValues[] = $pvo->getOptionValue();
            }
        }
        return $this->selectedOptionValues;
    }

    /**
     * @param OptionValue[] $values
     */
    public function setSelectedOptionValues(array $values): self
    {
        $this->selectedOptionValues = $values;

        return $this;
    }

    /**
     * @return Collection<int, OrderLines>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLines $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setProductVariant($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLines $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProductVariant() === $this) {
                $orderLine->setProductVariant(null);
            }
        }

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(float $tva): static
    {
        $this->tva = $tva ?: 20.0;

        return $this;
    }

    public function getPriceHT()
    {
        return $this->price / (1 + ($this->tva / 100));
    }

    public function getTvaAmount()
    {
        return $this->price - $this->getPriceHT();
    }
}
