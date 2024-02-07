<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Magasin;
use Doctrine\ORM\EntityManagerInterface;

class MagasinController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/magasins/all', name: 'magasinsAll', methods: ['GET'])]
    public function magasinsAll(): JsonResponse
    {
        $magasinRepository = $this->entityManager->getRepository(Magasin::class);
        $magasins = $magasinRepository->findAll();

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
    #[Route('/magasins/near', name: 'magasinsNear', methods: ['GET'])]
    public function magasinsNear(Request $request): JsonResponse
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

        if ($latitude === null || $longitude === null) {
            return new JsonResponse(['error' => 'Les coordonnees du user doivent etre fournies'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $magasins = $this->entityManager->getRepository(Magasin::class)->findAll();

        $formattedMagasins = [];
        foreach ($magasins as $magasin) {
            $distance = $this->calculateDistance($latitude, $longitude, $magasin->getLatitude(), $magasin->getLongitude());
            $formattedMagasins[] = [
                'id' => $magasin->getId(),
                'nom' => $magasin->getNom(),
                'adresse' => $magasin->getAdresse(),
                'zip' => $magasin->getZip(),
                'distance' => $distance,
            ];
        }

        usort($formattedMagasins, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $this->json($formattedMagasins);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Formule de calcul de la distance entre deux points géographiques (en km)
        // Rayon de la terre en kilomètres
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        // Distance en kilomètres
        return $earthRadius * $c;
    }
}
