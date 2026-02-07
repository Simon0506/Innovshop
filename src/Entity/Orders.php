<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    public const STATUT_CART = 'panier';
    public const STATUT_VALIDATED = 'validée';
    public const STATUT_PAID = 'payée';
    public const STATUT_DELIVERED = 'envoyée';
    public const STATUT_CANCELED = 'annulée';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date = null;

    /**
     * @var Collection<int, OrderLines>
     */
    #[ORM\OneToMany(targetEntity: OrderLines::class, mappedBy: 'orders', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $orderLines;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Addresses $deliveryAddress = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Addresses $billingAddress = null;

    #[ORM\Column(nullable: true)]
    private ?string $stripeSessionId = null;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

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
            $orderLine->setOrders($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLines $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getOrders() === $this) {
                $orderLine->setOrders(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function addProductVariant(ProductVariant $pv, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        foreach ($this->orderLines as $line) {
            if ($line->getProductVariant() === $pv) {
                $line->setQuantity($line->getQuantity() + $quantity);
                $line->updateSubtotal();
                $this->recalculateTotal();
                return;
            }
        }

        $line = new OrderLines();
        $line->setOrders($this);
        $line->setProductVariant($pv);
        $line->setQuantity($quantity);
        $line->setUnitPrice($pv->getPrice());
        $line->updateSubtotal();

        $this->orderLines->add($line);
        $this->recalculateTotal();
    }

    public function removeProductVariant(ProductVariant $pv, int $quantity = 1)
    {
        $quantity = max(1, $quantity);
        foreach ($this->orderLines as $line) {
            if ($line->getProductVariant() === $pv) {
                if ($line->getQuantity() > $quantity) {
                    $line->setQuantity($line->getQuantity() - $quantity);
                    $line->updateSubtotal();
                } else {
                    $this->orderLines->removeElement($line);
                }
                $this->recalculateTotal();
                return;
            }
        }
    }

    public function recalculateTotal()
    {
        $total = 0;
        foreach ($this->orderLines as $line) {
            $total += $line->getSubtotal();
        }

        $this->total = $total;
    }

    public function getDeliveryAddress(): ?Addresses
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?Addresses $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getBillingAddress(): ?Addresses
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?Addresses $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): static
    {
        $this->stripeSessionId = $stripeSessionId;
        return $this;
    }

    public function getProductsNumber(): int
    {
        $number = 0;
        foreach ($this->orderLines as $line) {
            $number += $line->getQuantity();
        }
        return $number;
    }

    public function getTotalHT(): float
    {
        $totalHT = 0;
        foreach ($this->orderLines as $line) {
            $totalHT += $line->getPriceHT();
        }
        return $totalHT;
    }

    public function getTotalTVA(): float
    {
        $totalTVA = 0;
        foreach ($this->orderLines as $line) {
            $totalTVA += $line->getTvaAmount();
        }
        return $totalTVA;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computeTotalOrder(): void
    {
        foreach ($this->orderLines as $line) {
            $line->computePrices();
        }
        $this->total = array_sum(array_map(function (OrderLines $line) {
            return $line->getSubtotal();
        }, $this->orderLines->toArray()));
    }
}
