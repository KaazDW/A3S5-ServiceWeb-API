<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
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
    public function magasinsNear(): JsonResponse
    {
//    TODO: "En tant qu’utilisateur non connecté, je peux consulter la liste des magasins près de chez moi"
        

        return $this->json($formattedMagasins);
    }
}
