<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Service\AuthService;
use App\Service\RequestTransformer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CreateCommentController extends AbstractController
{
    public function __invoke(Request $request, ManagerRegistry $doctrine, RequestTransformer $requestTransformer, AuthService $authService, IriConverterInterface $iriConverter)
    {
        $data = $requestTransformer->getRequestContent($request);
        $user = $authService->getUserFromRequest($request);
        $post = $iriConverter->getResourceFromIri($data["post"]);

        if (!$post) {
            return $this->json([
                "success" => false,
                "message" => "Le post est introuvable."
            ], 404);
        }

        $comment = new Comment();
        $comment->setUser($user)
            ->setContent($data["content"])
            ->setPost($post);

        $em = $doctrine->getManager();
        $em->persist($comment);
        $em->flush();

        return $this->json([
            "success" => true,
            "message" => "Commentaire enregistré."
        ]);
    }
}
