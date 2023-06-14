<?php

namespace App\Service;

use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class AuthService {

    private string $iv;
    private string $passphrase;
    private string $algo;

    public function __construct(private UserRepository $userRepository)
    {
        $this->iv = $_ENV["SSL_IV"];
        $this->passphrase = $_ENV["SSL_PASSPHRASE"];
        $this->algo = $_ENV["SSL_ALGO"];
    }

    public function encrypt(mixed $data) {
        $data = serialize($data);
        return openssl_encrypt($data, $this->algo, $this->passphrase, 0, $this->iv);
    }

    public function decrypt(string $data) {
        return unserialize(openssl_decrypt($data, $this->algo, $this->passphrase, 0, $this->iv));
    }

    public function getUserFromRequest(Request $request) {
        $token = $this->getRequestToken($request);

        if (!$token) {
            return false;
        }

        $decrypted = $this->decrypt($token);
        $now = new DateTime();

        if ($now->format("d/m/y H:i") > $decrypted["expire"]) {
            return false;
        }

        if (null === $user = $this->userRepository->find($decrypted["id"])) {
            return false;
        }

        return $user;
    }

    private function getRequestToken(Request $request) {
        if (strpos($request->getContentTypeFormat(), "json") !== false) {
            $content = json_decode($request->getContent(), true);
            return isset($content["token"]) ? $content["token"] : false;
        }

        return $request->get("token");
    }
}