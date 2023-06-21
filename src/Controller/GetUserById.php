<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthService;
use App\Service\RequestTransformer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class GetUserById extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine, private Security $security)
    {
    }
    public function __invoke(Request $request, AuthService $authService, RequestTransformer $requestTransformer, int $id): JsonResponse
    {
        $em = $this->doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Utilisateur n\'existe pas',
                'data' => null,
            ], Response::HTTP_NOT_FOUND);
        }

        $userData = [
            'username' => $user->getUsername(),
            'posts' => $user->getPosts(),
            'comments' => $user->getComments(),
            'description' => $user->getDescription(),
            'avatar' => $user->getAvatar(),
        ];

        return $this->json([
            'success' => true,
            'message' => 'Found.',
            'data' => $userData,
        ]);
    }

}