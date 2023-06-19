<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostCommentsController extends AbstractController
{
    public function __invoke(Post $post)
    {
        $comments = [];

        foreach ($post->getComments() as $comment) {
            $comments[] = [
                "content" => $comment->getContent(),
                "createdAt" => $comment->getCreatedAt(),
                "author" => [
                    "username" => $comment->getUser()->getUsername(),
                    "id" => $comment->getUser()->getId(),
                ]
            ];
        }

        return $this->json($comments);
    }
}
