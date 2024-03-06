<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\Key;
use Namshi\JOSE\SimpleJWS;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Commande;
use App\Entity\DetailsCommande;
use App\Entity\Magasin;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Firebase\JWT\JWT;

class CommandeController extends AbstractController
{


    #[Route('/commandes', name: 'create_commande', methods: ['POST'])]
    public function createCommande(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the token from the request header
        $token = $request->headers->get('Authorization');

        // Check if the token is valid
        if (!$this->isTokenValid($token)) {
            throw new AccessDeniedException('Seuls les utilisateurs connectés peuvent passer une commande.');
        }

        // Extract client_id from the token
//        $clientId = $this->getUser()->getUserIdentifier();

        // Retrieve the magasin_id and other details from the request body
        $data = json_decode($request->getContent(), true);
        $magasinId = $data['magasin_id'];
        $produits = $data['produits'];
        $status = 1; // Assuming the status is always 1

        // Create a new Commande instance
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus($status);

        // Retrieve the client and magasin entities
//        $client = $entityManager->getRepository(Utilisateur::class)->find($clientId);
        $magasin = $entityManager->getRepository(Magasin::class)->find($magasinId);

        // Set the client and magasin for the commande
//        $commande->setClientID($client);
        $commande->setMagasinID($magasin);

        // Persist the commande entity
        $entityManager->persist($commande);
        $entityManager->flush();

        // Add details for each product
        foreach ($produits as $produitData) {
            $produitId = $produitData['id'];
            $quantite = $produitData['quantite'];

            // Retrieve the produit entity
            $produit = $entityManager->getRepository(Produit::class)->find($produitId);

            // Create a new DetailsCommande instance
            $detailsCommande = new DetailsCommande();
            $detailsCommande->setProduitID($produit);
            $detailsCommande->setQuantite($quantite);
            $detailsCommande->setCommandeID($commande); // Set the relation to the commande

            // Persist the detailsCommande entity
            $entityManager->persist($detailsCommande);
        }

        // Flush changes to the database
        $entityManager->flush();

        // Placeholder return statement
        return new Response('Commande créée avec succès', Response::HTTP_CREATED);
    }

    private function isTokenValid($token): bool
    {
        try {
            $token = substr($token, 7);
            $secretKey = 'testsecretkey'; // Your secret key
            $jws = SimpleJWS::load($token); // Load the token
            $jws->verify($secretKey, 'HS256'); // Verify the token
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}