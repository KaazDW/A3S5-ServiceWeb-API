<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Magasin;

class MagasinController extends AbstractController
{
    /**
     * @Route("/magasins/all", name="magasinsAll", methods={"GET"})
     */
    public function magasinsAll(): JsonResponse
    {
        $magasins = $this->getDoctrine()->getRepository(Magasin::class)->findAll();

        $formattedMagasins = [];
        foreach ($magasins as $magasin) {
            $formattedMagasins[] = [
                'id' => $magasin->getId(),
                'nom' => $magasin->getNom(),
                'adresse' => $magasin->getAdresse(),
                'zip' => $magasin->getZip(),
            ];
        }

        return $this->json($formattedMagasins);
    }
}
