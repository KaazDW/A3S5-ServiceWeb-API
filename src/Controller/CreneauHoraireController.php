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

    /**
     * @throws \Exception
     */
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

        // Check if all required fields are present
        $requiredFields = ['date', 'heureDebut', 'heureFin'];

        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field]) || empty($requestData[$field])) {
                return new JsonResponse(['error' => 'Paramètre manquant : ' . $field], Response::HTTP_BAD_REQUEST);
            }
        }

        // Check if the date, heureDebut, and heureFin are in valid format
        $date = $requestData['date'];
        $heureDebut = $requestData['heureDebut'];
        $heureFin = $requestData['heureFin'];

        if (!strtotime($date) || !strtotime($heureDebut) || !strtotime($heureFin)) {
            return new JsonResponse(['error' => 'Format de date ou d\'heure invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Create a new CreneauHoraire entity
        $creneau = new CreneauHoraire();

        // Set the date and heure for the creneau
        $creneau->setDate(new \DateTime($date));
        $creneau->setHeureDebut(new \DateTime($heureDebut));
        $creneau->setHeureFin(new \DateTime($heureFin));

        // Persist the creneau entity
        $entityManager->persist($creneau);
        $entityManager->flush();

        // Return a successful response
        return new JsonResponse(['message' => 'Créneau créé avec succès'], Response::HTTP_CREATED);
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
    //gere la date ulterieur 
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


        // Récupérer la commande
        $commande = $entityManager->getRepository(Commande::class)->find($idCommande);

        // Vérifier si la commande existe
        if (!$commande) {
            return new Response('Commande non trouvée', Response::HTTP_NOT_FOUND);
        }
//        dd($userId);
//        dd($commande->getClientID()->getId());
        // Vérifier si la commande appartient à l'utilisateur connecté
        if ($commande->getClientID()->getId() !== $userId) {
            return new Response('Vous n\'êtes pas autorisé à modifier cette commande', Response::HTTP_FORBIDDEN);
        }

        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données requises sont présentes dans la requête
        if (!isset($data['creneau_id'])) {
            return new Response('Paramètre manquant pour ajouter le créneau à la commande', Response::HTTP_BAD_REQUEST);
        }

        // Récupérer le créneau
        $creneau = $entityManager->getRepository(CreneauHoraire::class)->find($data['creneau_id']);

        // Vérifier si le créneau existe
        if (!$creneau) {
            return new Response('Créneau non trouvé', Response::HTTP_NOT_FOUND);
        }

        // Ajouter le créneau à la commande
        $commande->setCreneauHoraire($creneau);

        // Enregistrer les modifications dans la base de données
        $entityManager->persist($commande);
        $entityManager->flush();

        return new Response('Créneau ajouté à la commande avec succès', Response::HTTP_OK);
    }

}