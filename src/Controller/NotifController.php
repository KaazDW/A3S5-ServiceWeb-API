<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Magasin;
use App\Entity\Notif;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotifController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/notify', name: 'notify', methods: ['POST'])]
    public function notifyCustomer(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer le token d'authentification de l'utilisateur
        $token = $request->headers->get('Authorization');

        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return new JsonResponse(['error' => 'Non autorisé'], Response::HTTP_UNAUTHORIZED);
        }

        // Décoder le token JWT
        $tokenParts = explode(".", $token);
        $tokenPayload = base64_decode($tokenParts[1]);

        if (!$tokenPayload) {
            return new JsonResponse(['error' => 'Token non valide'], Response::HTTP_UNAUTHORIZED);
        }

        $jwtPayload = json_decode($tokenPayload);

        // Vérifier si l'utilisateur a le rôle d'administrateur
        if (!property_exists($jwtPayload, 'roles') || !in_array('ROLE_ADMIN', $jwtPayload->roles)) {
            return new JsonResponse(['error' => 'Vous devez être administrateur pour envoyer une notification'], Response::HTTP_FORBIDDEN);
        }

        // Vérifier si la commande id est spécifiée dans le corps de la requête
        $data = json_decode($request->getContent(), true);
        if (!isset($data['command_id']) || !isset($data['client_id']) || !isset($data['contenu'])) {
            return new JsonResponse(['error' => 'Paramètres manquants dans la requête'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si l'utilisateur associé à client_id a le rôle ROLE_USER
        $client = $entityManager->getRepository(Utilisateur::class)->find($data['client_id']);
        if (!$client || !in_array('ROLE_USER', $client->getRoles())) {
            return new JsonResponse(['error' => 'Le client n\'a pas le rôle ROLE_USER'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer la commande avec l'ID spécifié
        $commandId = $data['command_id'];
        $command = $entityManager->getRepository(Commande::class)->find($commandId);

        if (!$command) {
            return new JsonResponse(['error' => 'Commande non trouvée'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si la commande appartient à l'utilisateur spécifié
        if ($command->getClientID()->getId() !== $data['client_id']) {
            return new JsonResponse(['error' => 'La commande n\'appartient pas à cet utilisateur'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si le statut de la commande est égal à 1
        if ($command->getStatus() !== 1) {
            return new JsonResponse(['error' => 'La commande doit avoir un statut de 1 pour envoyer une notification'], Response::HTTP_BAD_REQUEST);
        }

        // Créer une nouvelle notification
        $notif = new Notif();
        $notif->setContenu($data['contenu']);
        $notif->setClientID($client);
        $notif->setCommandeID($command);

        // Enregistrer la nouvelle notification dans la base de données
        $entityManager->persist($notif);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Notification envoyée avec succès'], Response::HTTP_OK);
    }


}