<?php


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
// * @ORM\Entity(repositoryClass=CreneauRepository::class)
// * @ORM\Table(name="creneau_horaire")
// */
//#[ORM\Entity(repositoryClass: CreneauRepository::class)]
//#[ORM\Table(name: "creneau_horaire")]
class Creneau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $horaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(\DateTimeInterface $horaire): static
    {
        $this->horaire = $horaire;

        return $this;
    }
}