<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetPostLikesController extends AbstractController
{
    public function __invoke(Post $post, PostRepository $postRepository)
    {
        return $this->json($post->getLikes());
    }
}
