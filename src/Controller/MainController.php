<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    private string $project_name = 'BEAT CONNECTION';

    #[Route('/', name: 'app_main')]

    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'project_name' => $this->project_name
        ]);
    }
}
