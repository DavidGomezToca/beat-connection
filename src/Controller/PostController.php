<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
    public function show($id): Response
    {
        if ($id <= 0) {
            return $this->redirect('/');
        }
        $post = $this->em->getRepository(Post::class)->find($id);
        if ($post) {
            return $this->render('post/post.html.twig', [
                'id' => $id,
                'post' => $post
            ]);
        } else {
            return $this->redirect('/');
        }
    }

    #[Route('/post-form', name: 'app_post_form')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $posts = $this->em->getRepository(Post::class)->findAllPosts();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $user = $this->em->getRepository(User::class)->find(1);
            $url = str_replace(" ", "-", $form->get('title')->getData());
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ther is an error with your file');
                }
                $post->setFile($newFilename);
            }
            $post->setUser($user);
            $post->setUrl($url);
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('app_post', ['id' => $post->getId()]);
        }
        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
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
