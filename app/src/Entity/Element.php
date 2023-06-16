<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\ElementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElementRepository::class)]
class Element
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $name = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $availability = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity;

    #[ORM\Column(type: Types::STRING)]
    private ?string $elementId = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $currency = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $additional = null;

    /**
     * @param string|null $name
     * @param int|null $price
     * @param bool|null $availability
     * @param string|null $description
     * @param int|null $quantity
     * @param int|null $elementId
     */
    public function __construct(?string $elementId, ?string $name, ?float $price, ?bool $availability, ?string $description, ?int $quantity, ?string $currency, ?string $additional)
    {
        $this->name = $name;
        $this->price = $price;
        $this->availability = $availability;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->elementId = $elementId;
        $this->currency = $currency;
        $this->additional = $additional;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isAvailability(): ?bool
    {
        return $this->availability;
    }

    public function setAvailability(?bool $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getElementId(): ?int
    {
        return $this->elementId;
    }

    public function setElementId(int $elementId): self
    {
        $this->elementId = $elementId;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAdditional(): ?string
    {
        return $this->additional;
    }

    public function setAdditional(?string $additional): self
    {
        $this->additional = $additional;

        return $this;
    }
}
