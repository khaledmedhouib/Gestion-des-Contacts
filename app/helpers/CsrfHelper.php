<?php
// app/helpers/CsrfHelper.php

class CsrfHelper
{
    public static function generate(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function validate(string $token): bool
    {
        return isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateOrFail(string $token): void
    {
        if (!self::validate($token)) {
            throw new \RuntimeException('Token de sécurité invalide. Veuillez réessayer.');
        }
    }
}