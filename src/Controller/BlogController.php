<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function blog(ManagerRegistry $doctrine): Response
    {
        // Traer todos los posts desde la BD
        $posts = $doctrine->getRepository(Post::class)->findBy([], ['publishedAt' => 'DESC']);

        return $this->render('page/blog.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/post/{slug}', name: 'single_post')]
    public function singlePost(string $slug, ManagerRegistry $doctrine): Response
    {
        $post = $doctrine->getRepository(Post::class)->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('Post no encontrado');
        }

        return $this->render('page/single_post.html.twig', [
            'post' => $post
        ]);
    }
}
