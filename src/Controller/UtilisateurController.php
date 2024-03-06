<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Firebase\JWT\JWT;


class UtilisateurController extends AbstractController
{
    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données nécessaires sont présentes
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Les champs email et password sont requis'], 400);
        }

        // Vérifier si l'utilisateur existe déjà dans la base de données
        $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);

        if ($existingUser !== null) {
            return new JsonResponse(['message' => 'Un utilisateur avec cet email existe déjà'], 400);
        }

        // Créer une nouvelle instance de l'entité Utilisateur
        $user = new Utilisateur();
        $user->setEmail($data['email']);
        $user->setPrenom($data['prenom'] ?? null);
        $user->setNom($data['nom'] ?? null);
        $user->setType(['type1', 'type2']);

        // Encoder et définir le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Enregistrer l'utilisateur dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Retourner une réponse JSON avec un message de succès
        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], 201);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Les champs email et password sont requis'], 400);
        }

        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $data['email']]);

        if ($user === null || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['message' => 'Identifiants incorrects'], 400);
        }

        $issuedAt = time();
        $expirationTime = $issuedAt + 60; // token valide pour 60 secondes
        $payload = array(
            'userid' => $user->getId(),
            'iat' => $issuedAt,
            'exp' => $expirationTime
        );

        $token_jwt = JWT::encode($payload, 'testsecretkey', 'HS256');

        return new JsonResponse(['token' => $token_jwt]);
    }
}
