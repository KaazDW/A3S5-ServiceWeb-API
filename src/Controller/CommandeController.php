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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommandeController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/commandes', name: 'create_commande', methods: ['POST'])]
    public function createCommande(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the token from the request body
        $data = json_decode($request->getContent(), true);
        $token = $data['token'];

        // Check if the token is valid
        if (!$this->isTokenValid($token)) {
            throw new AccessDeniedException('Seuls les utilisateurs connectés peuvent passer une commande.');
        }

        $magasinRepository = $entityManager->getRepository(Magasin::class);
        $magasin = $magasinRepository->find($data['magasin_id']);

        if (!$magasin) {
            return new Response('Magasin non trouvé', Response::HTTP_NOT_FOUND);
        }

        $commande = new Commande();
        $commande->setClientID($this->getUser());
        $commande->setMagasinID($magasin);
        $commande->setDateCommande(new \DateTime());
        $commande->setStatus(0); // Set status to 0

        $entityManager->persist($commande);

        $produitRepository = $entityManager->getRepository(Produit::class);

        foreach ($data['produits'] as $produitData) {
            $produit = $produitRepository->find($produitData['id']);

            if (!$produit) {
                return new Response('Produit non trouvé', Response::HTTP_NOT_FOUND);
            }

            $detailsCommande = new DetailsCommande();
            $detailsCommande->setCommandeID($commande);
            $detailsCommande->setProduitID($produit);
            $detailsCommande->setQuantite($produitData['quantite']);

            $entityManager->persist($detailsCommande);
        }

        $entityManager->flush();

        return new Response('Commande créée avec succès', Response::HTTP_CREATED);
    }

    private function isTokenValid($token)
    {
        $tokenStorage = $this->tokenStorage->getToken();

        // Check if a token exists
        if (!$tokenStorage) {
            return false;
        }

        // Get the current user
        $user = $tokenStorage->getUser();

        // Check if the user is logged in
        if (!$user) {
            return false;
        }

        // Check if the token matches the user's token
        if ($user->getToken() != $token) { // Change this line
            return false;
        }

        // Check if the token has expired
        $now = new \DateTime();
        if ($user->getTokenExpiration() < $now) {
            return false;
        }

        return true;
    }
}