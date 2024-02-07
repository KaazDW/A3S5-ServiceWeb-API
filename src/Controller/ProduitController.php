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
}
