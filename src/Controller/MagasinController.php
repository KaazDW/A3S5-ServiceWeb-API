<?php
namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Magasin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


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
        // Récupérer les coordonnées de l'utilisateur à partir des paramètres de requête GET
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

        // Vérifier si les coordonnées ont été fournies et si ce sont des chiffres
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return new JsonResponse(['error' => 'Les coordonnées doivent être des chiffres'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si les coordonnées sont réalistes
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return new JsonResponse(['error' => 'Les coordonnées fournies sont irréalistes'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupérer tous les magasins
        $magasins = $this->entityManager->getRepository(Magasin::class)->findAll();

        // Initialiser le tableau pour stocker les magasins formatés avec les distances
        $formattedMagasins = [];

        // Calculer la distance de chaque magasin et l'ajouter au tableau formaté
        foreach ($magasins as $magasin) {
            $distance = $this->calculateDistance($latitude, $longitude, $magasin->getLatitude(), $magasin->getLongitude());
            $formattedMagasins[] = [
                'id' => $magasin->getId(),
                'nom' => $magasin->getNom(),
                'ville' => $magasin->getVille(),
                'adresse' => $magasin->getAdresse(),
                'zip' => $magasin->getZip(),
                'distance' => $distance,
            ];
        }

        // Trier les magasins par distance
        usort($formattedMagasins, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        // Sélectionner les 5 premiers magasins les plus proches
        $nearestMagasins = array_slice($formattedMagasins, 0, 5);

        // Retourner la réponse JSON avec les magasins les plus proches
        return $this->json($nearestMagasins);
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

    #[Route('/magasins/stock/{id}', name: 'magasinStock', methods: ['GET'])]
    public function magasinStock($id): JsonResponse
    {
        $magasin = $this->entityManager->getRepository(Magasin::class)->find($id);

        if (!$magasin) {
            return $this->json(['message' => 'Magasin non trouvé'], 404);
        }

        $stocks = $magasin->getStocks()->toArray();
        $formattedStocks = array_map(function ($stock) {
            $produit = $stock->getProduitID();
            return [
                'id' => $produit->getId(),
                'name' => $produit->getNom(),
                'description' => $produit->getDescription(),
                'prix' => $produit->getPrix()
            ];
        }, $stocks);

        return $this->json($formattedStocks);
    }

    #[Route('/new/magasins', name: 'newMagasins', methods: ['POST'])]
    public function createNewStore(Request $request, EntityManagerInterface $entityManager): Response
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
        if (property_exists($jwtPayload, 'roles')) {
            $roleUser = $jwtPayload->roles;

            if (!in_array('ROLE_ADMIN', $roleUser)) {
                return new JsonResponse(['error' => 'Vous devez être administrateur pour créer un magasin'], Response::HTTP_FORBIDDEN);
            }
        } else {
            // Gérer le cas où la propriété 'roles' n'est pas définie dans le payload JWT
            return new JsonResponse(['error' => 'Informations sur les rôles manquantes dans le token JWT'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données requises sont présentes dans la requête
        $requiredFields = ['nom', 'adresse', 'zip', 'ville', 'pays', 'latitude', 'longitude'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return new JsonResponse(['error' => 'Paramètre manquant : ' . $field], Response::HTTP_BAD_REQUEST);
            }
        }

        // Vérifier si les coordonnées sont des chiffres
        if (!is_numeric($data['latitude']) || !is_numeric($data['longitude'])) {
            return new JsonResponse(['error' => 'Les coordonnées de latitude et longitude doivent être des chiffres'], Response::HTTP_BAD_REQUEST);
        }

        // Créer un nouveau magasin
        $magasin = new Magasin();
        $magasin->setNom($data['nom']);
        $magasin->setAdresse($data['adresse']);
        $magasin->setZip($data['zip']);
        $magasin->setVille($data['ville']);
        $magasin->setPays($data['pays']);
        $magasin->setLatitude($data['latitude']);
        $magasin->setLongitude($data['longitude']);

        // Enregistrer le nouveau magasin dans la base de données
        $entityManager->persist($magasin);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Nouveau magasin créé avec succès'], Response::HTTP_CREATED);
    }
}
