<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Produit $produitID = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Magasin $magasinID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduitID(): ?Produit
    {
        return $this->produitID;
    }

    public function setProduitID(?Produit $produitID): static
    {
        $this->produitID = $produitID;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getMagasinID(): ?Magasin
    {
        return $this->magasinID;
    }

    public function setMagasinID(?Magasin $magasinID): static
    {
        $this->magasinID = $magasinID;

        return $this;
    }
}
