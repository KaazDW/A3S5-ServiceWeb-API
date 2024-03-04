<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;

class ProduitController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/produit/{id}', name: 'produitDetails', methods: ['GET'])]
    public function produitDetails($id): JsonResponse
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
