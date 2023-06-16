<?php

namespace App\Entity;

use App\Repository\AdditionalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdditionalRepository::class)]
class Additional
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $photoUrl = null;

    #[ORM\Column(length: 100)]
    private ?string $element_id = null;

    /**
     * @param string|null $photoUrl
     * @param string|null $element_id
     */
    public function __construct(?string $photoUrl, ?string $element_id)
    {
        $this->photoUrl = $photoUrl;
        $this->element_id = $element_id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getElementId(): ?string
    {
        return $this->element_id;
    }

    public function setElementId(string $element_id): self
    {
        $this->element_id = $element_id;

        return $this;
    }
}
