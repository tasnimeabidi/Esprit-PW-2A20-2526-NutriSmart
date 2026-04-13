<?php
/**
 * Réponses JSON communes pour l’API CRUD.
 */
declare(strict_types=1);

final class JsonApi
{
    public static function lireCorpsJson(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /** @param array<string, mixed>|list<mixed> $payload */
    public static function envoyer(int $code, $payload): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    public static function erreur(int $code, string $message): void
    {
        self::envoyer($code, ['error' => $message]);
    }
}
