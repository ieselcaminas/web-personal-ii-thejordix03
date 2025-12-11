<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostController extends AbstractController
{
    #[Route('/blog/new', name: 'new_post')]
    public function newPost(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        // ⛔ 1. SI NO ESTÁ LOGEADO → A LOGIN
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 📌 Crear slug
            $post->setSlug($slugger->slug($post->getTitle()));

            // 📌 Setear usuario
            $post->setPostUser($this->getUser());

            // 📌 Inicializar contadores
            $post->setNumLikes(0);
            $post->setNumComments(0);
            $post->setPublishedAt(new \DateTime());

            // 📌 Procesar imagen
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/posts',
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception("Error al subir la imagen");
                }

                $post->setImage($newFilename);
            }

            // 📌 Guardar en BD
            $em = $doctrine->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('single_post', [
                'slug' => $post->getSlug()
            ]);
        }

        return $this->render('blog/new_post.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
