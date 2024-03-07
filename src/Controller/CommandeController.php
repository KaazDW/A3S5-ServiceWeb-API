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
    #[Route('/commandes/new', name: 'create_commande', methods: ['POST'])]
    public function createCommande(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the token from the request header
        $token = $request->headers->get('Authorization');

        // Vérifie si le token JWT est vide ou s'il ne commence pas par "Bearer "
        if (!$token || strpos($token, 'Bearer ') !== 0) {
            return new Response('non autorisé', Response::HTTP_UNAUTHORIZED);
        }
        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        // Vérifie si le décodage a réussi
        if (!$tokenPayload) {
            return new Response('token non valide', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        // Vérifie si la charge utile du JWT contient l'identifiant de l'utilisateur
//        if (!isset($jwtPayload->username)) {
//            return new Response('Utilisateur non connecté', Response::HTTP_UNAUTHORIZED);
//        }

        $tokenParts = explode(".", $token);

        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
//        $mailUser=$jwtPayload->username;

        $userId = $jwtPayload->userid;

        // Retrieve the magasin_id and other details from the request body
        $data = json_decode($request->getContent(), true);
        $magasinId = $data['magasin_id'];
        $produits = $data['produits'];
        $status = 1; // Assuming the status is always 1

        // Retrieve the client entity using the user ID
        $client = $entityManager->getRepository(Utilisateur::class)->find($userId);

        // Retrieve the magasin entity
        $magasin = $entityManager->getRepository(Magasin::class)->find($magasinId);

        // Create a new Commande instance
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus($status);
        $commande->setClientID($client); // Set the client for the commande
        $commande->setMagasinID($magasin); // Set the magasin for the commande

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
    #[Route('/commandes/all', name: 'get_all_commandes', methods: ['GET'])]
    public function getAllCommandes(EntityManagerInterface $entityManager): Response
    {
        // Retrieve all Commande entities
        $commandes = $entityManager->getRepository(Commande::class)->findAll();

        // Convert the commandes to an array
        $commandesArray = [];
        foreach ($commandes as $commande) {
            $commandesArray[] = [
                'id' => $commande->getId(),
                'dateCommande' => $commande->getDateCommande(),
                'status' => $commande->getStatus(),
                'clientID' => $commande->getClientID(),
                'magasinID' => $commande->getMagasinID(),
            ];
        }

        // Return the commandes as a JSON response
        return new Response(json_encode($commandesArray), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}