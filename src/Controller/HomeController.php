<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController {

    #[Route('/', name: 'home')]
    function index(Request $request): Response {
        return $this->render('home/index.html.twig', [
            'title' => 'Recettes',
        ]); 
    }
}
