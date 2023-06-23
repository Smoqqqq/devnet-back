<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route(path: '/search', name: 'app_search')]
    public function search(Request $request, UserRepository $userRepository, PostRepository $postRepository): Response
    {
        $searchTerm = strtolower($request->query->get('q'));

        $users = $userRepository->createQueryBuilder('u')
            ->where('LOWER(u.username) LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

        $userResponse = [];
        foreach ($users as $user) {
            $userResponse[] = [
                'username' => $user->getUsername(),
                'id' => $user->getId(),
                'avatar' => $user->getAvatar(),
                'description' => $user->getDescription(),
                'Nbcomments' => count($user->getComments()),
                'Posts' => count($user->getPosts())
            ];
        }

        $posts = $postRepository->createQueryBuilder('p')
            ->join('p.author', 'a')
            ->where('LOWER(p.title) LIKE :term OR p.createdAt LIKE :term')
            ->setParameter('term', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();

        $postResponse = [];
        foreach ($posts as $post) {
            $postResponse[] = [
                'title' => $post->getTitle(),
                'createdAt' => $post->getCreatedAt()->format('c'),
                'username' => $post->getAuthor()->getUsername(),
                'userAvatar' => $post->getAuthor()->getAvatar(),
                'content' => $post->getContent(),
            ];
        }

        $results = array_merge($userResponse, $postResponse);

        return $this->json($results);
    }
}