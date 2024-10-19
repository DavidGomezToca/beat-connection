<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            if ($postEntity) {
                $postMessage = [
                    'id' => $postEntity->getId(),
                    'title' => $postEntity->getTitle(),
                    'type' => $postEntity->getType(),
                    'description' => $postEntity->getDescription()
                ];
            } else {
                $postMessage = "The post with ID: $id doesn't exist.";
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
            'custom_post' => $customPostMessage
        ]);
    }

    #[Route('/insert/post/{id}', name: 'app_post_insert')]
    public function insert($id)
    {
        if ($id <= 0) {
            return $this->redirect('/');
        }
        dump('Serching User');
        $user = $this->em->getRepository(User::class)->find($id);
        dump('User found');
        if ($user) {
            $post = new Post(
                'Inserted Post For User ID: ' . $id,
                'Opinion',
                'Description Inserted Post',
                'Description File',
                'description-url'
            );
            $post->setUser($user);
            $this->em->persist($post);
            $this->em->flush();
            return new JsonResponse([
                'operation' => 'insert',
                'succes' => true,
                'creation_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } else {
            return $this->redirect('/');
        }
    }

    #[Route('/update/post/{id}', name: 'app_post_update')]
    public function update($id)
    {
        if ($id <= 0) {
            return $this->redirect('/');
        }
        $post = $this->em->getRepository(Post::class)->find($id);
        if ($post) {
            $post->setTitle('Updated Title');
            $this->em->flush();
            return new JsonResponse([
                'operation' => 'update',
                'succes' => true,
                'update_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } else {
            return $this->redirect('/');
        }
    }

    #[Route('/remove/post/{id}', name: 'app_post_remove')]
    public function remove($id)
    {
        if ($id <= 0) {
            return $this->redirect('/');
        }
        $post = $this->em->getRepository(Post::class)->find($id);
        if ($post) {
            $this->em->remove($post);
            $this->em->flush();
            return new JsonResponse([
                'operation' => 'remove',
                'succes' => true,
                'update_time' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        } else {
            return $this->redirect('/');
        }
    }
}
