<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Service\RequestTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
class UpdateUserController
{
    private $passwordEncoder;
    private $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, AuthService $authService, RequestTransformer $requestTransformer): JsonResponse
    {
        $user = $authService->getUserFromRequest($request);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Vous devez être connecté.',
            ], 401);
        }

        $data = $requestTransformer->getRequestContent($request);

        $currentPassword = $data['currentPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;
        $email = $data['email'] ?? null;
        $username = $data['username'] ?? null;
        $avatar = $data['avatar'] ?? null;
        $description = $data['description'] ?? null;

        if (!$this->passwordEncoder->isPasswordValid($user, $currentPassword)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Identifiants incorrects.',
            ], 401);
        }

        if ($newPassword) {
            $user->setPassword($this->passwordEncoder->hashPassword($user, $newPassword));
        }

        if ($email && $email !== $user->getEmail()) {
            $user->setEmail($email);
        }

        if ($username && $username !== $user->getUsername()) {
            $user->setUsername($username);
        }

        if ($avatar && $avatar !== $user->getAvatar()) {
            $user->setAvatar($avatar);
        }
        if ($description && $description !== $user->getDescription()) {
            $user->setDescription($description);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Utilisateur updated',
        ]);
    }
}