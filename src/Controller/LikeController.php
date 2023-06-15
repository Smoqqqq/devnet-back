<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\LikeRepository;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    public function __invoke(Post $post, LikeRepository $likeRepository, AuthService $authService, Request $request)
    {
        $user = $authService->getUserFromRequest($request);

        $like = $likeRepository->findOneBy(["user" => $user, "post" => $post]);

        return $this->json($like);
    }
}
