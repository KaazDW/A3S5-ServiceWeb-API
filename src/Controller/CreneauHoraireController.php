<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CreneauHoraire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreneauHoraireController extends AbstractController
{
    private $security;

    public function __construct(EntityManagerInterface $entityManager,Security $security)
    {
        $this->security = $security;

    }
    #[Route('/creneau/new', name: 'new_creneau', methods: ['POST'])]
    public function newCreneau(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->headers->get('Authorization');

        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return new Response('Utilisateur non autorisé', Response::HTTP_UNAUTHORIZED);
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
                return new Response('Vous devez être administrateur pour créer un creneau', Response::HTTP_FORBIDDEN);
            }
        } else {
            return new Response('Informations sur les rôles manquantes dans le token JWT', Response::HTTP_BAD_REQUEST);
        }

        // Decode the request content into an array
        $requestData = json_decode($request->getContent(), true);

        // Create a new CreneauHoraire entity
        $creneau = new CreneauHoraire();

        // Set the date and heure for the creneau
        $creneau->setDate(new \DateTime($requestData['date']));
        $creneau->setHeureDebut(new \DateTime($requestData['heureDebut']));
        $creneau->setHeureFin(new \DateTime($requestData['heureFin']));

        // Persist the creneau entity
        $entityManager->persist($creneau);
        $entityManager->flush();

        // Return a successful response
        return new Response('Créneau créé avec succès', Response::HTTP_CREATED);
    }

    #[Route('/creneau/all', name: 'all_creneau', methods: ['GET'])]
    public function allCreneau(EntityManagerInterface $entityManager): Response
    {
        // Retrieve all CreneauHoraire entities
        $creneaux = $entityManager->getRepository(CreneauHoraire::class)->findAll();

        // Convert the creneaux to an array of arrays
        $creneauxArray = array_map(function ($creneau) {
            return [
                'id' => $creneau->getId(),
                'date' => $creneau->getDate()->format('Y-m-d'),
                'heureDebut' => $creneau->getHeureDebut()->format('H:i:s'),
                'heureFin' => $creneau->getHeureFin()->format('H:i:s'),
            ];
        }, $creneaux);

        // Return the creneaux as a JSON response
        return new Response(json_encode($creneauxArray), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


    /**
     * @throws \Exception
     */
    #[Route('/book/creneaux/{idCommande}', name: 'book_creneau', methods: ['POST'])]
    public function reserverCreneau(Request $request, EntityManagerInterface $entityManager, $idCommande,Security $security): Response
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
//        dd($userId);

        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si la date, l'heure de début et l'heure de fin sont présentes dans la requête
        if (!isset($data['date']) || !isset($data['heure_debut']) || !isset($data['heure_fin'])) {
            return new JsonResponse(['message' => 'Date, heure de début et heure de fin sont requis.'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer la commande correspondante à l'identifiant fourni
        $commande = $entityManager->getRepository(Commande::class)->find($idCommande);

        // Vérifier si la commande existe
        if (!$commande) {
            return new JsonResponse(['message' => 'Commande non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si la commande appartient à l'utilisateur connecté
        if ($commande->getClientID() !== $userId) {
            return new JsonResponse(['message' => 'Vous n\'êtes pas autorisé à accéder à cette commande.'], Response::HTTP_FORBIDDEN);
        }

        // Créer un nouvel objet CreneauHoraire
        $creneauHoraire = new CreneauHoraire();
        $creneauHoraire->setDate(new \DateTime($data['date']));
        $creneauHoraire->setHeureDebut(new \DateTime($data['heure_debut']));
        $creneauHoraire->setHeureFin(new \DateTime($data['heure_fin']));

        // Enregistrer le créneau horaire dans la base de données
        $entityManager->persist($creneauHoraire);
        $entityManager->flush();

        // Associer l'identifiant du créneau horaire à la commande correspondante
        $commande = $entityManager->getRepository(Commande::class)->find($idCommande);
        if (!$commande) {
            return new JsonResponse(['message' => 'Commande non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        // Associer le créneau horaire à la commande
        $commande->setCreneauHoraire($creneauHoraire);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Créneau horaire réservé avec succès.'], Response::HTTP_CREATED);
    }
}