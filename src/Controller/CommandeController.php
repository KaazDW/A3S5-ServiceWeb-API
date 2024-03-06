<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
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

        // Rest of your code to create a commande...

        // Placeholder return statement
        return new Response('Commande créée avec succès', Response::HTTP_CREATED);
    }

    private function isTokenValid($token)
    {
        try {
            $token = substr($token, 7);
            $secretKey = 'testsecretkey'; // Your secret key
            $allowed_algorithms = ['HS256'];
            $decoded = JWT::decode($token, $secretKey, $allowed_algorithms);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}