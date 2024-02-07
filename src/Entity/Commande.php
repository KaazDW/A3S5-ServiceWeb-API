<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Utilisateur $clientID = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Magasin $magasinID = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\OneToMany(targetEntity: DetailsCommande::class, mappedBy: 'commandeID')]
    private Collection $detailsCommandes;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?CreneauHoraire $horaire = null;

    public function __construct()
    {
        $this->detailsCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientID(): ?Utilisateur
    {
        return $this->clientID;
    }

    public function setClientID(?Utilisateur $clientID): static
    {
        $this->clientID = $clientID;

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

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, DetailsCommande>
     */
    public function getDetailsCommandes(): Collection
    {
        return $this->detailsCommandes;
    }

    public function addDetailsCommande(DetailsCommande $detailsCommande): static
    {
        if (!$this->detailsCommandes->contains($detailsCommande)) {
            $this->detailsCommandes->add($detailsCommande);
            $detailsCommande->setCommandeID($this);
        }

        return $this;
    }

    public function removeDetailsCommande(DetailsCommande $detailsCommande): static
    {
        if ($this->detailsCommandes->removeElement($detailsCommande)) {
            // set the owning side to null (unless already changed)
            if ($detailsCommande->getCommandeID() === $this) {
                $detailsCommande->setCommandeID(null);
            }
        }

        return $this;
    }

    public function getHoraire(): ?CreneauHoraire
    {
        return $this->horaire;
    }

    public function setHoraire(?CreneauHoraire $horaire): static
    {
        $this->horaire = $horaire;

        return $this;
    }
}
