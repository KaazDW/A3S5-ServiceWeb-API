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
use Symfony\Component\Security\Core\Security;

class CommandeController extends AbstractController
{
    #[Route('/commandes/new/{id}', name: 'create_commande', methods: ['POST'])]
    public function createCommande($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the token from the request header
        $token = $request->headers->get('Authorization');

        // Check if the token is empty or doesn't start with "Bearer "
        if (!$token || strpos($token, 'Bearer ') !== 0) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        // Check if the decoding was successful
        if (!$tokenPayload) {
            return new Response('Invalid token', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        // Get the user ID from the JWT payload
        $userId = $jwtPayload->userid;

        // Retrieve the user entity using the user ID
        $user = $entityManager->getRepository(Utilisateur::class)->find($userId);

        // Retrieve the product entity using the product ID
        $product = $entityManager->getRepository(Produit::class)->find($id);

        // Retrieve the magasin_id from the request body
        $data = json_decode($request->getContent(), true);
        $magasinId = $data['magasin_id'];
        $status = 1; // Assuming the status is always 1

        // Retrieve the magasin entity
        $magasin = $entityManager->getRepository(Magasin::class)->find($magasinId);

        // Create a new Commande instance
        $commande = new Commande();
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus($status);
        $commande->setClientID($user); // Set the client for the commande
        $commande->setMagasinID($magasin); // Set the magasin for the commande

        // Persist the commande entity
        $entityManager->persist($commande);
        $entityManager->flush();

        // Create a new DetailsCommande instance
        $detailsCommande = new DetailsCommande();
        $detailsCommande->setProduitID($product);
        $detailsCommande->setQuantite(1); // Assuming the quantity is always 1
        $detailsCommande->setCommandeID($commande); // Set the relation to the commande

        // Persist the detailsCommande entity
        $entityManager->persist($detailsCommande);

        // Flush changes to the database
        $entityManager->flush();

        // Placeholder return statement
        return new Response('Commande créée avec succès', Response::HTTP_CREATED);
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


    #[Route('/commandes/me', name: 'my_orders', methods: ['GET'])]
    public function myOrders(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the token from the request header
        $token = $request->headers->get('Authorization');

        // Check if the token is empty or doesn't start with "Bearer "
        if (!$token || strpos($token, 'Bearer ') !== 0) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        // Check if the decoding was successful
        if (!$tokenPayload) {
            return new Response('Invalid token', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        // Get the user ID from the JWT payload
        $userId = $jwtPayload->userid;

        // Log the user ID
        error_log("User ID: $userId");

        // Retrieve the user entity using the user ID
        $user = $entityManager->getRepository(Utilisateur::class)->find($userId);

        // If there is no user with this ID, return a 404 Not Found response
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        // Get the user's orders
        $orders = $entityManager->getRepository(Commande::class)->findBy(['clientID' => $user]);

        // Log the orders
//        error_log("Orders: " . print_r($orders, true));

        // Convert the orders to an array of arrays
        $ordersArray = array_map(function ($order) {
            return [
                'id' => $order->getId(),
                'dateCommande' => $order->getDateCommande(),
                'status' => $order->getStatus(),
                'clientID' => $order->getClientID()->getId(),
                'magasinID' => $order->getMagasinID()->getId(),
            ];
        }, $orders);

        // Return the orders as a JSON response
        return new Response(json_encode($ordersArray), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/commandes/find/{id}', name: 'find_commande', methods: ['GET'])]
    public function findCommande($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the token from the request header
        $token = $request->headers->get('Authorization');

        // Check if the token is empty or doesn't start with "Bearer "
        if (!$token || strpos($token, 'Bearer ') !== 0) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        // Check if the decoding was successful
        if (!$tokenPayload) {
            return new Response('Invalid token', Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        // Get the user ID from the JWT payload
        $userId = $jwtPayload->userid;

        // Retrieve the user entity using the user ID
        $user = $entityManager->getRepository(Utilisateur::class)->find($userId);

        // If there is no user with this ID, return a 404 Not Found response
        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        // Retrieve the Commande entity with the given ID
        $commande = $entityManager->getRepository(Commande::class)->find($id);

        // If there is no Commande with this ID, return a 404 Not Found response
        if (!$commande) {
            return new Response('Commande not found', Response::HTTP_NOT_FOUND);
        }

        // Check if the authenticated user is the owner of the Commande
        if ($commande->getClientID()->getId() !== $user->getId()) {
            return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        // Convert the Commande to an array
        $commandeArray = [
            'id' => $commande->getId(),
            'dateCommande' => $commande->getDateCommande(),
            'status' => $commande->getStatus(),
            'clientID' => $commande->getClientID()->getId(),
            'magasinID' => $commande->getMagasinID()->getId(),
        ];

        // Return the Commande as a JSON response
        return new Response(json_encode($commandeArray), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}