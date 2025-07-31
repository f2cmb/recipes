<?php

namespace App\Controller;

use App\Service\ImageResizerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/resize/images/{path}', name: 'image_resize', requirements: ['path' => '.+'])]
    public function resize(string $path, Request $request, ImageResizerService $imageResizer): Response
    {
        $params = $request->query->all();
        
        return $imageResizer->getResizedImageResponse($path, $params);
    }
}