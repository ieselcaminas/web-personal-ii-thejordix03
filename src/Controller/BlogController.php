<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private $posts = [
        1 => [
            'title' => 'Recuerdo aquel dia que me cai con la bicicleta...',
            'subtitle' => 'Los problemas pequeños,causan un daño grande',
            'author' => 'Jordi',
            'date' => 'Septiembre 12,2024'
        ],
        2 => [
            'title' => 'Creo que vi un OVNI en el Desierto de las Palmas ',
            'subtitle' => '',
            'author' => 'Jordi',
            'date' => 'September 18, 2020'
        ],
        3 => [
            'title' => 'Primero el uno y luego el 2',
            'subtitle' => '',
            'author' => 'Start Bootstrap',
            'date' => 'Agosto 24, 2023'
        ],
        4 => [
            'title' => 'El Fracaso no es una opción',
            'subtitle' => 'Creer es vivir',
            'author' => 'Jordi',
            'date' => 'Julio 8, 2023'
        ],
    ];

    #[Route('/blog', name: 'blog')]
    public function blog(): Response
    {
        return $this->render('page/blog.html.twig', [
            'posts' => $this->posts
        ]);
    }

    #[Route('/post/{id}', name: 'single_post')]
    public function singlePost(int $id): Response
    {
        if (!isset($this->posts[$id])) {
            throw $this->createNotFoundException('Post no encontrado');
        }

        return $this->render('page/single_post.html.twig', [
            'post' => $this->posts[$id]
        ]);
    }
}
