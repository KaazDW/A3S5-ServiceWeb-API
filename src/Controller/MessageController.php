<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Produit;
use App\Entity\Vendeur;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController

{
    private $entityManager;
    private $security;


    public function __construct(EntityManagerInterface $entityManager,Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;

    }

    #[Route('/message', name: 'message_create', methods: ['POST'])]
    public function message(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);

        if (!isset($data['contenu']) || !isset($data['destinataire_id'])) {
            return new JsonResponse(['message' => 'Contenu et destinataire_id sont requis.'], 400);
        }

        // Récupérer l'ID du destinataire depuis les données de la requête
         $destinataireId = $data['destinataire_id'];

        // Charger l'entité Vendeur à partir de l'ID
        $destinataire = $entityManager->getRepository(Vendeur::class)->find($destinataireId);

        // Vérifier si le destinataire existe
        if (!$destinataire) {
            return new JsonResponse(['message' => 'Le destinataire spécifié n\'existe pas.'], 404);
        }

        // Créer une nouvelle instance de Message
        $message = new Message();
        $message->setContenu($data['contenu']);
        $message->setExpediteur($data['email']);
        $message->setDestinataire($destinataire);

        // Enregistrement du message dans la base de données
        $entityManager->persist($message);
        $entityManager->flush();

        // Répondre avec un message de succès
        return new JsonResponse(['message' => 'Message envoyé avec succès.'], 201);
    }


//    #[Route('/notifyClient', name: 'notify_client', methods: ['POST'])]
//    public function notifyClients(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        // Vérifiez si l'utilisateur connecté est un administrateur
//        if (!$this->isUserAdmin()) {
//            throw new AccessDeniedException('Vous n\'avez pas les autorisations nécessaires pour effectuer cette action.');
//        }
//
//        // Récupérer les données de la requête
//        $data = json_decode($request->getContent(), true);
//
//        // Valider les données de la requête
//        if (!isset($data['contenu']) || !isset($data['destinataire_id'])) {
//            return new JsonResponse(['message' => 'Contenu et destinataire sont requis.'], 400);
//        }
//
//        // Créer une nouvelle instance de Message
//        $message = new Message();
//        $message->setContenu($data['contenu']);
//
//        // Récupérer l'expéditeur (administrateur connecté)
//        $expediteur = $this->getUser();
//
//        // Récupérer le destinataire (dans ce cas, le client)
//        $destinataireId = $data['destinataire_id'];
//
//        // Enregistrement de l'expéditeur et du destinataire
//        $message->setExpediteur($expediteur);
//        $message->setDestinataire($destinataireId);
//
//        // Enregistrement du message dans la base de données
//        $this->entityManager->persist($message);
//        $this->entityManager->flush();
//
//        // Répondre avec un message de succès
//        return new JsonResponse(['message' => 'Message envoyé avec succès.'], 201);
//    }
//
//    private function isUserAdmin(): bool
//    {
//        $user = $this->security->getUser();
//
//        // Vérifier si l'utilisateur est authentifié et s'il a le type d'administrateur
//        return $user && in_array('ROLE_ADMIN', $user->getRoles(), true);
//    }

}
