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


#[AsController]
class CreatePostController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    public function __invoke(Request $request, AuthService $authService, RequestTransformer $requestTransformer): JsonResponse
    {
        $user = $authService->getUserFromRequest($request);

        if (!$user) {
            return $this->json([
                "success" => false,
                "message" => "Vous devez être connecté."
            ], 401);
        }

        $requestContent = $requestTransformer->getRequestContent($request);

        $content = $requestContent["content"];
        $title = $requestContent["title"];

        if (!$content || !$title) {
            return $this->json([
                "success" => false,
                "message" => "Des données sont manquantes, veuillez remplir le titre et le contenu du post."
            ], 400);
        }

        $post = new Post();
        $post->setContent($content)
            ->setTitle($title)
            ->setAuthor($user);

        $em = $this->doctrine->getManager();
        $em->persist($post);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Post enregistré.",
            "data" => $post
        ]);
    }
}
