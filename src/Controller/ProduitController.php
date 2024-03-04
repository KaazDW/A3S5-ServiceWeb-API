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

        // Récupérer les statistiques du produit
        $stats = [
            'nb_ventes' => $produit->getNbVentes(),
            // Ajoutez d'autres statistiques si nécessaire
        ];

        // Vérifier la disponibilité en stock du produit
        $stock = $this->entityManager->getRepository(Stock::class)->findOneBy(['produit' => $produit]);

        $disponibilite = false;
        $magasin = null;
        if ($stock && $stock->getQuantite() > 0) {
            $disponibilite = true;
            $magasin = $stock->getMagasin()->getNom();
        }

        // Préparation des données à retourner
        $formattedProduit = [
            'id' => $produit->getId(),
            'nom' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'prix' => $produit->getPrix(),
            'stats' => $stats,
            'disponibilite' => $disponibilite,
            'magasin' => $magasin,
        ];

        return $this->json($formattedProduit);
    }
}
