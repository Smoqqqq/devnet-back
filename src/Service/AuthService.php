<?php

namespace App\Service;

class AuthService {

    private string $iv;
    private string $passphrase;
    private string $algo;

    public function __construct()
    {
        $this->iv = $_ENV["SSL_IV"];
        $this->passphrase = $_ENV["SSL_PASSPHRASE"];
        $this->algo = $_ENV["SSL_ALGO"];
    }

    public function encrypt(string $data) {
        return openssl_encrypt($data, $this->algo, $this->passphrase, 0, $this->iv);
    }

    public function decrypt(string $data) {
        return openssl_decrypt($data, $this->algo, $this->passphrase, 0, $this->iv);
    }
}