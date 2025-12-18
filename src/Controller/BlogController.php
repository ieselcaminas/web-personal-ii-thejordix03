<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; // Agregar esta línea para importar Request
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Post;
use App\Entity\Comment; // Agregar esta línea para importar la clase Comment
use App\Form\CommentFormType; // Agregar esta línea para importar CommentFormType


class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog')]
    public function blog(ManagerRegistry $doctrine): Response
    {
        // Obtener todos los posts
        $posts = $doctrine->getRepository(Post::class)->findBy([], ['publishedAt' => 'DESC']);

        // Obtener los 5 posts más recientes
        $recents = $doctrine->getRepository(Post::class)->findRecents(); 

        return $this->render('page/blog.html.twig', [
            'posts' => $posts,
            'recents' => $recents,
        ]);
    }

    #[Route('/post/{slug}', name: 'single_post')]
    public function singlePost(string $slug, ManagerRegistry $doctrine, Request $request): Response
    {
        $postRepository = $doctrine->getRepository(Post::class);
        $commentRepository = $doctrine->getRepository(Comment::class);

        // Obtener el post por el slug
        $post = $postRepository->findOneBy(['slug' => $slug]);

        if (!$post) {
            throw $this->createNotFoundException('Post no encontrado');
        }

        // Crear formulario de comentario
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            // Asociar el comentario con el post
            $comment->setPost($post);
            $comment->setPublishedAt(new \DateTime());
            $comment->setUser($this->getUser());  // Usuario logueado

            // Guardar el comentario en la base de datos
            $em = $doctrine->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('single_post', ['slug' => $slug]);
        }

        // Obtener los comentarios del post
        $comments = $commentRepository->findBy(['post' => $post], ['publishedAt' => 'DESC']);

        // Pasar el formulario y los comentarios a la vista
        return $this->render('page/single_post.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }
    #[Route('/single_post/{slug}/like', name: 'post_like')]
public function like(ManagerRegistry $doctrine, string $slug): Response
{
    $repository = $doctrine->getRepository(Post::class);
    $post = $repository->findOneBy(['slug' => $slug]);

    if ($post) {
        $post->like(); // ⬅️ usamos el método de la entidad
        $em = $doctrine->getManager();
        $em->flush();
    }

    return $this->redirectToRoute('single_post', [
        'slug' => $slug
    ]);
}

}
