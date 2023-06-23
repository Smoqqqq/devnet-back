<?php

namespace App\Controller;

use App\Entity\Post;
use App\Service\AuthService;
use App\Service\RequestTransformer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

#[AsController]
class DeletePostController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine, private Security $security)
    {
    }

    public function __invoke(Request $request, AuthService $authService, RequestTransformer $requestTransformer, int $id): JsonResponse
    {
        $user = $authService->getUserFromRequest($request);

        if (!$user) {
            return $this->json([
                "success" => false,
                "message" => "Vous devez être connecté."
            ], 401);
        }

        $em = $this->doctrine->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (!$post) {
            return $this->json([
                "success" => false,
                "message" => "Le post demandé n'existe pas."
            ], 404);
        }

        if ($post->getAuthor() !== $user && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json([
                "success" => false,
                "message" => "Vous n'êtes pas autorisé à supprimer ce post."
            ], 403);
        }

        $em->remove($post);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Post supprimé."
        ]);
    }
}