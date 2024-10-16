<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\DBAL\Exception\ConnectionException;
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
        try {
            $post = $this->em->getRepository(Post::class)->find($id);
            if (!$post) {
                $posts = "This post doesn't exist";
            } else {
                $posts = $post;
            }
        } catch (ConnectionException $e) {
            $posts = "Database connection error. Please try again later.";
        } catch (\Exception $e) {
            $posts = "An unexpected error occurred. Please try again later.";
        }

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
