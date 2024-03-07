<?php

namespace App\Controller;

use App\Entity\CreneauHoraire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreneauHoraireController extends AbstractController
{
    #[Route('/creneau/new', name: 'new_creneau', methods: ['POST'])]
    public function newCreneau(Request $request, EntityManagerInterface $entityManager): Response
    {
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
}