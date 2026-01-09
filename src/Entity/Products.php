<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Products
{

    public const STATUT_STOCK_OK = 'En stock';
    public const STATUT_RUPTURE = 'Rupture de stock';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $stock = null;

    /**
     * @var Collection<int, Categories>
     */
    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'products')]
    private Collection $category;

    /**
     * @var Collection<int, Options>
     */
    #[ORM\ManyToMany(targetEntity: Options::class, inversedBy: 'products')]
    private Collection $option;

    /**
     * @var Collection<int, OrderLines>
     */
    #[ORM\OneToMany(targetEntity: OrderLines::class, mappedBy: 'product')]
    private Collection $orderLines;

    #[ORM\Column(nullable: true)]
    private ?bool $une = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dateAddUne = null;

    #[ORM\Column(length: 255)]
    private string $slug;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->option = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
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

    /**
     * @return Collection<int, Categories>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Categories $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Categories $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Options>
     */
    public function getOption(): Collection
    {
        return $this->option;
    }

    public function addOption(Options $option): static
    {
        if (!$this->option->contains($option)) {
            $this->option->add($option);
        }

        return $this;
    }

    public function removeOption(Options $option): static
    {
        $this->option->removeElement($option);

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
            $orderLine->setProduct($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLines $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProduct() === $this) {
                $orderLine->setProduct(null);
            }
        }

        return $this;
    }

    public function isUne(): ?bool
    {
        return $this->une;
    }

    public function setUne(?bool $une): static
    {
        $this->une = $une;

        return $this;
    }

    public function getDateAddUne(): ?\DateTimeImmutable
    {
        return $this->dateAddUne;
    }

    public function setDateAddUne(?\DateTimeImmutable $dateAddUne): static
    {
        $this->dateAddUne = $dateAddUne;

        return $this;
    }

    public function getSlug(): ?string
    {
        if (empty($this->slug)) {
            $slugger = new AsciiSlugger();
            $this->slug = strtolower($slugger->slug($this->title));
        }
        return $this->slug;
    }
}
