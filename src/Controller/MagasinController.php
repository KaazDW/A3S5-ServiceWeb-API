<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MagasinController extends AbstractController
{
    /**
     * @Route("/magasins-pres-de-chez-moi", name="magasins_pres_de_chez_moi", methods={"GET"})
     */
    public function magasinsPresDeChezMoi(): JsonResponse
    {
        // Ici, vous mettrez la logique pour récupérer les magasins près de chez l'utilisateur
        // Par exemple, en utilisant une entité Magasin avec Doctrine
        $magasins = $this->getDoctrine()->getRepository(Magasin::class)->findAll();

        // Formatage des données pour la réponse JSON
        $formattedMagasins = [];
        foreach ($magasins as $magasin) {
            $formattedMagasins[] = [
                'id' => $magasin->getId(),
                'nom' => $magasin->getNom(),
                'adresse' => $magasin->getAdresse(),
                // Ajoutez d'autres champs si nécessaire
            ];
        }

        // Réponse JSON contenant la liste des magasins
        return $this->json($formattedMagasins);
    }
}

// Path: src/Controller/MagasinController.php
