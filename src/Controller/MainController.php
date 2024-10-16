<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route('/', name: 'homepage')]
    public function homepage(): Response
    {
        return new Response('Beat Connection');
    }
}
