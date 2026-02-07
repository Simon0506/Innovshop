<?php

namespace App\Entity;

use App\Repository\OrderLinesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLinesRepository::class)]
class OrderLines
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $subtotal = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $orders = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $unitPrice = null;

    #[ORM\ManyToOne(inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductVariant $productVariant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): static
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function updateSubtotal()
    {
        if ($this->unitPrice === null || $this->quantity === null) {
            $this->subtotal = 0;
            return;
        }
        $this->subtotal = $this->unitPrice * $this->quantity;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
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

    public function getTva(): ?float
    {
        return $this->productVariant ? $this->productVariant->getTva() : null;
    }

    public function getPuht(): ?float
    {
        if ($this->unitPrice === null) {
            return null;
        }
        $tvaRate = $this->getTva() ?? 20.0;
        return $this->unitPrice / (1 + ($tvaRate / 100));
    }

    public function getPriceHT(): ?float
    {
        if ($this->unitPrice === null || $this->quantity === null) {
            return null;
        }
        $tvaRate = $this->getTva() ?? 20.0;
        return ($this->unitPrice * $this->quantity) / (1 + ($tvaRate / 100));
    }

    public function getTvaAmount(): ?float
    {
        if ($this->productVariant === null || $this->quantity === null) {
            return null;
        }
        $tvaRate = $this->productVariant->getTva() ?? 20.0;
        return ($this->getPuht() * $this->quantity) * ($tvaRate / 100);
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computePrices()
    {
        if ($this->productVariant !== null) {
            $this->unitPrice = $this->productVariant->getPrice();
        }
        $this->updateSubtotal();
    }
}
