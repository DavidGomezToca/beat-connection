<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/post/{id}', name: 'app_post')]

    public function index($id): Response
    {
        $posts = $this->em->getRepository(Post::class)->findBy([
            'id' => 1,
            'title' => 'User 001 Post 001'
        ]);
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
