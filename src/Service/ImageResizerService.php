<?php

namespace App\Service;

use League\Glide\Server;
use Symfony\Component\HttpFoundation\Response;


class ImageResizerService
{
    public function __construct(
        private Server $glideServer,
        private string $projectDir
    ) {}

    public function getResizedImageResponse(string $path, array $params = []): Response
    {
        // Limiter la largeur maximale à 500px
        if (isset($params['w'])) {
            $params['w'] = min((int)$params['w'], 500);
        } else {
            $params['w'] = 500;
        }

        // Conserver le ratio en ne spécifiant que la largeur
        unset($params['h']);

        try {
            // makeImage retourne le chemin du cache, pas les données
            $cachePath = $this->glideServer->makeImage($path, $params);

            // Lire le fichier en cache généré par Glide
            $cacheFullPath = $this->projectDir . '/var/cache/glide/' . $cachePath;


            if (!file_exists($cacheFullPath)) {
                throw new \RuntimeException("Le fichier en cache n'existe pas: " . $cacheFullPath);
            }

            $imageData = file_get_contents($cacheFullPath);

            $response = new Response($imageData);
            $response->headers->set('Content-Type', $this->getMimeType($path));
            $response->headers->set('Cache-Control', 'public, max-age=2592000');

            return $response;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors du redimensionnement de l\'image: ' . $e->getMessage());
        }
    }

    private function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg'
        };
    }

    public function getResizedImageUrl(string $imagePath, int $width = 500): string
    {
        $width = min($width, 500); // Limiter à 500px max
        return "/resize/images/{$imagePath}?w={$width}";
    }
}
