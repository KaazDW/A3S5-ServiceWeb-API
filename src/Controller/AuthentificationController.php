<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthentificationController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(EntityManagerInterface $entityManager, Request $request, UserPasswordEncoderInterface $encoder, JWTTokenManagerInterface $jwtManager): JsonResponse {
        $credentials = json_decode($request->getContent(), true);
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user || !$encoder->isPasswordValid($user, $credentials['password'])) {
            return new JsonResponse(['message' => 'Identifiants invalides'], 401);
        }
        $token = $jwtManager->create($user);

        return new JsonResponse(['token' => $token]);
    }
}
