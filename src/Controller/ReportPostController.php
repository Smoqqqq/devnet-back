<?php

namespace App\Controller;

use App\Service\AuthService;
use App\Entity\Reports;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

#[AsController]
class ReportPostController
{
    private $entityManager;

    public function __construct(
        private ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager,
        private Security $security,
    ) {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, AuthService $authService, int $id): JsonResponse
    {
        // $postId = $request->attributes->get('postId');
        $em = $this->doctrine->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        // $user = $this->security->getUser();
        $user = $authService->getUserFromRequest($request);

        if (!$user) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Vous devez être connecté.',
            ], 401);
        }

        if (!$post) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Post not found.',
            ], 404);
        }

        $reportsRepository = $this->entityManager->getRepository(Reports::class);
        $report = $reportsRepository->findOneBy([
            'reporter' => $user,
            'reportedPost' => $post,
        ]);

        if ($report) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Vos avez déjà signalé ce post',
            ], 409);
        }

        $report = new Reports();
        $report->setReporter($user);
        $report->setReportedPost($post);
        $report->setCreatedAt(new \DateTimeImmutable());
        $report->setStatus(1);
    

        $this->entityManager->persist($report);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Post reported successfully.',
        ]);
    }
}