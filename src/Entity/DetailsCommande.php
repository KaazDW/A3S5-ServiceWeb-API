<?php

namespace App\Entity;

use App\Repository\DetailsCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailsCommandeRepository::class)]
class DetailsCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'detailsCommandes')]
    private ?Commande $commandeID = null;

    #[ORM\ManyToOne(inversedBy: 'detailsCommandes')]
    private ?Produit $produitID = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCommandeID(): ?Commande
    {
        return $this->commandeID;
    }

    public function setCommandeID(?Commande $commandeID): static
    {
        $this->commandeID = $commandeID;

        return $this;
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
}
