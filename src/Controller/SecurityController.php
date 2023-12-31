<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthService;
use DateInterval;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/auth', name: 'app_login')]
    public function login(): Response
    {
        throw new Exception("Cette route n'existe pas");
    }

    #[Route(path: '/login', name: 'app_auth')]
    public function auth(UserPasswordHasherInterface $hasher, Request $request, Security $security, UserRepository $userRepository, AuthService $authService): Response
    {
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $duration = $request->request->get("remember_me") ? "P10D" : "P1D";

        /** @var PasswordAuthenticatedUserInterface */
        $user = $userRepository->findOneBy(["email" => $email]);

        if (!$user) {
            return $this->json([
                "success" => false,
                "message" => "L'utilisateur n'existe pas."
            ], 404);
        }

        if (!$hasher->isPasswordValid($user, $password)) {
            return $this->json([
                "success" => false,
                "message" => "Identifiants incorrects."
            ]);
        }

        /** @var UserInterface */
        $user = $user;

        $security->login($user);

        /** @var User */
        $user = $user;

        $roles = implode('', $user->getRoles());
        $expire = new DateTime();
        $oneDayInterval = new DateInterval($duration);
        $expire->add($oneDayInterval);

        $data = [
            // "roles" => $roles, // Supprimé car crée un problème lors de la sérialisation, c'est pas nécessaire car on à déjà l'id
            "id" => $user->getId(),
            "expire" => $expire->getTimestamp()
        ];

        
        $token = $authService->encrypt($data);

        return $this->json([
            "success" => true,
            "message" => "Connexion réussit",
            "token" => $token,
            "userId" => $user->getId()
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route("/register", name: "app_register")]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher)
    {
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $username = $request->request->get("username");

        $user = new User();
        $user->setEmail($email)
            ->setUsername($username)
            ->setRoles(["ROLE_USER"])
            ->setPassword($hasher->hashPassword($user, $password))
            ->SetAvatar('https://scontent-cdg4-1.xx.fbcdn.net/v/t1.6435-9/180978949_314228950059549_1005358403722529104_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=09cbfe&_nc_ohc=BVsnxwGklC4AX8zc3-b&_nc_ht=scontent-cdg4-1.xx&oh=00_AfBOXLRUaSMMu4RIWFFpy-n-50KrbKVOWEsspBGSO6YX8Q&oe=64BA4B8E')
            ->SetDescription('');

        $em = $doctrine->getManager();

        try {
            $em->persist($user);
            $em->flush();
        } catch (UniqueConstraintViolationException $e) {
            return $this->json([
                "success" => false,
                "message" => "Un compte existe déjà avec cette adresse email."
            ]);
        } catch (\Exception $e) {
            return $this->json([
                "success" => false,
                "message" => "Une erreur c'est produite.",
                "error" => $e->getMessage()
            ]);
        }

        return $this->json([
            "success" => true,
            "message" => "Compte crée"
        ]);
    }

    #[Route("/check-auth", name: "app_user_check_auth")]
    public function checkAuth(Request $request, AuthService $authService, UserRepository $userRepository) {
        $token = $request->request->get("token");

        if (!$token) {
            return $this->json([
                "success" => false,
                "message" => "Veuillez renseigner un token dans les paramètres GET"
            ]);
        }

        $decrypted = $authService->decrypt($token);
        $now = new DateTime();

        if ($now->getTimestamp() > $decrypted["expire"]) {
            return $this->json([
                "success" => false,
                "message" => "La connexion à expiré, veuillez vous connecter à nouveau",
            ]);
        }

        $user = $userRepository->find($decrypted["id"]);

        if (!$user) {
            return $this->json([
                "success" => false,
                "message" => "Token invalide : utilisateur inconnu."
            ]);
        }

        return $this->json([
            "success" => true,
            "message" => "OK",
            "user" => $user
        ]);
    }

    #[Route("/test")]
    public function test(AuthService $authService, Request $request) {
        dd($authService->getUserFromRequest($request));
    }
}
