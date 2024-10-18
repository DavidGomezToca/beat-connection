<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
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
        if ($id <= 0) {
            return $this->redirect('/');
        }
        try {
            $postEntity = $this->em->getRepository(Post::class)->find($id);
            if (!$postEntity) {
                $postMessage = "The post with ID: $id doesn't exist.";
            } else {
                $postMessage = [
                    'id' => $postEntity->getId(),
                    'title' => $postEntity->getTitle(),
                    'type' => $postEntity->getType(),
                    'description' => $postEntity->getDescription()
                ];
            }
        } catch (ConnectionException $e) {
            $postMessage = "Database connection error. Please try again later.";
        } catch (\Exception $e) {
            $postMessage = "An unexpected error occurred. Please try again later.";
        }
        try {
            $customPostEntity = $this->em->getRepository(Post::class)->findPost((string)($id + 1.));
            if (!$customPostEntity) {
                $customPostMessage = "The custom post with ID: $id doesn't exist.";
            } else {
                $customPostMessage = "Custom Post ID: " . $customPostEntity['id'];
            }
        } catch (NoResultException $e) {
            $customPostMessage = "The custom post with ID: " . ($id + 1) . " doesn't exist.";
        } catch (ConnectionException $e) {
            $customPostMessage = "Database connection error. Please try again later.";
        } catch (\Exception $e) {
            $customPostMessage = "An unexpected error occurred. Please try again later.";
        }
        return $this->render('post/index.html.twig', [
            'post' => $postMessage,
            'custom_post' => $customPostMessage,
        ]);
    }
}
