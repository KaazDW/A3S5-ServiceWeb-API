<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;

class ProduitController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/produits/all', name: 'produitsDisponibles', methods: ['GET'])]
    public function produitsDisponibles(): JsonResponse
    {
        $produits = $this->entityManager->getRepository(Produit::class)->findAll();

        $formattedProduits = [];
        foreach ($produits as $produit) {
            $formattedProduits[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'prix' => $produit->getPrix(),
            ];
        }

        return $this->json($formattedProduits);
    }

    #[Route('/produits/{id}', name: 'produitDetails', methods: ['GET'])]
    public function produitDetails($id): JsonResponse
    {
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouve'], 404);
        }

        // Récupérer les informations sur les magasins dans lesquels le produit est disponible
        $magasins = [];
        foreach ($produit->getMagasins() as $magasin) {
            $magasins[] = [
                'id' => $magasin->getId(),
                'nom' => $magasin->getNom(),
                'adresse' => $magasin->getAdresse(),
                'zip' => $magasin->getZip(),
            ];
        }

        $formattedProduit = [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'prix' => $produit->getPrix(),
            'magasins' => $magasins,
        ];

        return $this->json($formattedProduit);
    }
}
