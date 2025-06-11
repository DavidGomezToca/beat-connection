<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllPosts()
    {
        return $this->getEntityManager()->createQuery('
            SELECT post.id, post.title, post.type, post.description, post.file, post.creation_date, post.url, user.id AS user_id, user.email AS user_username
            FROM App\Entity\Post post
            JOIN post.user user
            ORDER BY post.id DESC
            ')
            ->getResult();
    }

    public function findPost($id)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT post.id
                FROM App\Entity\Post post
                WHERE post.id =:id
            ')
            ->setParameter('id', $id)
            ->getSingleResult();
    }
}
