<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToMany(targetEntity: Magasin::class, inversedBy: 'produits')]
    private Collection $magasinID;

    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'produitID')]
    private Collection $stocks;

    public function __construct()
    {
        $this->magasinID = new ArrayCollection();
        $this->stocks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * @return Collection<int, Magasin>
     */
    public function getMagasinID(): Collection
    {
        return $this->magasinID;
    }

    public function addMagasinID(Magasin $magasinID): static
    {
        if (!$this->magasinID->contains($magasinID)) {
            $this->magasinID->add($magasinID);
        }

        return $this;
    }

    public function removeMagasinID(Magasin $magasinID): static
    {
        $this->magasinID->removeElement($magasinID);

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setProduitID($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getProduitID() === $this) {
                $stock->setProduitID(null);
            }
        }

        return $this;
    }
}
