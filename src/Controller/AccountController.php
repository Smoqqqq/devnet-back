<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class AccountController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                "success" => false,
                "message" => "Vous n'êtes pas connecté."
            ], 401);
        }

        return $this->json([
            "success" => true,
            "data" => $user
        ]);
    }
}
