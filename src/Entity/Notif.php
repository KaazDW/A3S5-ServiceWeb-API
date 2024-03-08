<?php

namespace App\Entity;

use App\Repository\NotifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotifRepository::class)]
class Notif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\ManyToOne(inversedBy: 'notifs')]
    private ?Utilisateur $clientID = null;

    #[ORM\ManyToOne(inversedBy: 'notifs')]
    private ?Commande $commandeID = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
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

    public function getCommandeID(): ?Commande
    {
        return $this->commandeID;
    }

    public function setCommandeID(?Commande $commandeID): static
    {
        $this->commandeID = $commandeID;

        return $this;
    }
}
