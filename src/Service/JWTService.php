<?php

namespace App\Service;

use DateTimeImmutable;
use phpDocumentor\Reflection\Types\Boolean;

class JWTService 
{
    /**
     * Generate a JWT token
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param integer $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
    
            $payload["iat"] = $now->getTimestamp();
            $payload["exp"] = $exp;
        };

        $base64header = base64_encode(json_encode($header));
        $base64payload = base64_encode(json_encode($payload));

        $base64header = str_replace(["+", "/", "="], ["-", "_", ""], $base64header);
        $base64payload = str_replace(["+", "/", "="], ["-", "_", ""], $base64payload);

        $secret = base64_decode($secret);

        $signature = hash_hmac("sha256", "$base64header.$base64payload", $secret, true);

        $base64signature = base64_encode($signature);

        $base64signature = str_replace(["+", "/", "="], ["-", "_", ""], $base64signature);

        $jwt = "$base64header.$base64payload.$base64signature";

        return $jwt;
    }

    /**
     * Check if a JWT token is valid
     *
     * @param string $token
     * @return boolean
     */
    public function isValid(string $token): bool
    {
        return preg_match(
            "/^[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+$/",
            $token
        ) === 1;
    }

    /**
     * Get the payload of a JWT token
     *
     * @param string $token
     * @return array
     */
    public function getPayload(string $token): array
    {
        if (!$this->isValid($token)) return [];

        $parts = explode(".", $token);

        $payload = $parts[1];

        $payload = str_replace(["-", "_"], ["+", "/"], $payload);

        $payload = base64_decode($payload);

        $payload = json_decode($payload, true);

        return $payload;
    }

    /**
     * Get the header of a JWT token
     *
     * @param string $token
     * @return array
     */
    public function getHeader(string $token): array
    {
        if (!$this->isValid($token)) return [];

        $parts = explode(".", $token);

        $header = $parts[0];

        $header = str_replace(["-", "_"], ["+", "/"], $header);

        $header = base64_decode($header);

        $header = json_decode($header, true);

        return $header;
    }

    /**
     * Check if a JWT token is expired
     *
     * @param string $token
     * @return boolean
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        if (empty($payload)) return true;

        $exp = $payload["exp"];

        $now = new DateTimeImmutable();

        return $now->getTimestamp() > $exp;
    }
    
    public function check(string $token, string $secret)
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $verifToken === $token;
    }
}