<?php

namespace App\Controller;

use App\Entity\Magasin;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    #[Route('/produit/all', name: 'allProduits', methods: ['GET'])]
    public function allProduits(): JsonResponse
    {
        $produits = $this->entityManager->getRepository(Produit::class)->findAll();

        $formattedProduits = array_map(function ($produit) {
            return [
                'id' => $produit->getId(),
                'name' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'prix' => $produit->getPrix()
            ];
        }, $produits);

        return $this->json($formattedProduits);
    }
    #[Route('/produit/find/{id}', name: 'produitDetails', methods: ['GET'])]
    public function produitDetails($id): JsonResponse
    {
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], 404);
        }

        $stock = $produit->getStocks();
        $isInStock = $stock ? true : false;

        $formattedProduit = [
            'id' => $produit->getId(),
            'name' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'prix' => $produit->getPrix(),
            'inStock' => $isInStock
        ];

        return $this->json($formattedProduit);
    }

    #[Route('/update-stock', name: 'update_stock', methods: ['PUT'])]
    public function updateStock(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->headers->get('Authorization');

        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return new Response('non autorisé', Response::HTTP_UNAUTHORIZED);
        }

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        if (!$tokenPayload) {
            return new Response('token non valide', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        if (property_exists($jwtPayload, 'roles')) {
            $roleUser = $jwtPayload->roles;

            if (!in_array('ROLE_ADMIN', $roleUser)) {
                return new Response('Vous devez être administrateur pour mettre à jour le stock', Response::HTTP_FORBIDDEN);
            }
        } else {
            return new Response('Informations sur les rôles manquantes dans le token JWT', Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        // Vérifier si les données requises sont présentes dans la requête
        if (!isset($data['produit_id']) || !isset($data['magasin_id']) || !isset($data['quantite'])) {
            return new Response('Paramètres manquants pour la mise à jour du stock', Response::HTTP_BAD_REQUEST);
        }

        // Récupérer l'entité Produit et l'entité Magasin correspondant aux IDs fournis
        $produit = $entityManager->getRepository(Produit::class)->find($data['produit_id']);
        $magasin = $entityManager->getRepository(Magasin::class)->find($data['magasin_id']);

        if (!$produit || !$magasin) {
            return new Response('Produit ou magasin non trouvé', Response::HTTP_NOT_FOUND);
        }

        // Rechercher l'entrée Stock correspondant au produit et au magasin
        $stock = $entityManager->getRepository(Stock::class)->findOneBy([
            'produitID' => $produit,
            'magasinID' => $magasin
        ]);

        // Si aucune entrée Stock n'existe, créez-en une nouvelle
        if (!$stock) {
            $stock = new Stock();
            $stock->setProduitID($produit);
            $stock->setMagasinID($magasin);
        }

        // Mettre à jour la quantité de stock avec la nouvelle quantité spécifiée
        $stock->setQuantite($data['quantite']);

        $entityManager->persist($stock);
        $entityManager->flush();

        return new Response('Produit modifié avec succès', Response::HTTP_OK);

    }

    #[Route('/remove/product', name: 'remove_product', methods: ['DELETE'])]
    public function removeProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->headers->get('Authorization');

        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return new Response('non autorisé', Response::HTTP_UNAUTHORIZED);
        }

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        if (!$tokenPayload) {
            return new Response('token non valide', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        if (property_exists($jwtPayload, 'roles')) {
            $roleUser = $jwtPayload->roles;

            if (!in_array('ROLE_ADMIN', $roleUser)) {
                return new Response('Vous devez être administrateur pour retirer un produit du stock', Response::HTTP_FORBIDDEN);
            }
        } else {
            return new Response('Informations sur les rôles manquantes dans le token JWT', Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);

        // Vérifier si les données requises sont présentes dans la requête
        if (!isset($data['produit_id']) || !isset($data['magasin_id'])) {
            return new Response('Paramètres manquants pour retirer un produit du stock', Response::HTTP_BAD_REQUEST);
        }

        // Récupérer l'entité Produit et l'entité Magasin correspondant aux IDs fournis
        $produit = $entityManager->getRepository(Produit::class)->find($data['produit_id']);
        $magasin = $entityManager->getRepository(Magasin::class)->find($data['magasin_id']);

        if (!$produit || !$magasin) {
            return new Response('Produit ou magasin non trouvé', Response::HTTP_NOT_FOUND);
        }

        // Rechercher l'entrée Stock correspondant au produit et au magasin
        $stock = $entityManager->getRepository(Stock::class)->findOneBy([
            'produitID' => $produit->getId(),
            'magasinID' => $magasin->getId()
        ]);

        // Vérifier si une entrée Stock existe pour le produit et le magasin spécifiés
        if ($stock) {
            // Supprimer l'entité Stock de la base de données
            $entityManager->remove($stock);
            $entityManager->flush();

            return new Response('Produit retiré du stock avec succès', Response::HTTP_OK);
        } else {
            return new Response('Produit non trouvé dans le stock', Response::HTTP_NOT_FOUND);
        }
    }


}
