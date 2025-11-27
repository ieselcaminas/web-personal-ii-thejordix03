<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Category;
use App\Form\ImageFormType;
use App\Form\CategoryFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/images', name: 'app_images')]
    public function images(
        ManagerRegistry $doctrine,
        Request $request,
        SluggerInterface $slugger
    ): Response {

        // Recuperar todas las imágenes
        $images = $doctrine->getRepository(Image::class)->findAll();

        // Crear el formulario
        $image = new Image();
        $form = $this->createForm(ImageFormType::class, $image);
        $form->handleRequest($request);

        // Procesar el formulario
        if ($form->isSubmitted() && $form->isValid()) {

            // 1️⃣ Recuperamos el fichero subido
            $uploadedFile = $form->get('file')->getData();

            if ($uploadedFile) {
                // 2️⃣ Generamos un nombre seguro
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                // 3️⃣ Movemos la imagen a /public/uploads/images
                try {
                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );

                } catch (FileException $e) {
                    $this->addFlash('danger', 'Error al subir la imagen.');
                }

                // 4️⃣ Guardamos el nombre del archivo
                $image->setFile($newFilename);
            }

            // 5️⃣ Guardamos en la BD
            $entityManager = $doctrine->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            $this->addFlash('success', 'Imagen subida correctamente.');

            return $this->redirectToRoute('app_images');
        }

        return $this->render('admin/images.html.twig', [
            'form' => $form->createView(),
            'images' => $images
        ]);
    }

    #[Route('/admin', name: 'admin_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_images');
    }

    #[Route('/admin/categories', name: 'app_categories')]
    public function categories(ManagerRegistry $doctrine, Request $request): Response
    {
        $repositorio = $doctrine->getRepository(Category::class);
        $categories = $repositorio->findAll();

        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría guardada correctamente.');

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('admin/categories.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }
    #[Route('/admin/categories/delete/{id}', name: 'app_delete_category', methods:['POST'])]
     public function deleteCategory(ManagerRegistry $doctrine, Category $category, Request $request): Response
      {
     if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Categoría eliminada correctamente.');
     }

     return $this->redirectToRoute('app_categories');
}

}
