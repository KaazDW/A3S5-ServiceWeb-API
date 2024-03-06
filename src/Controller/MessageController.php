<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController

{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/message', name: 'message', methods: ['GET'])]
    public function message($id): JsonResponse
    {
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], 404);
        }

        $formattedProduit = [
            'id' => $produit->getId(),
            'name' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'prix' => $produit->getPrix()
        ];

        return $this->json($formattedProduit);
    }
}
