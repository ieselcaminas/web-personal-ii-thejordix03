<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'index')]
public function index(ManagerRegistry $doctrine): Response
{
    /** @var \App\Repository\CategoryRepository $repository */
    $repository = $doctrine->getRepository(Category::class);

    $categories = $repository->createQueryBuilder('c')
        ->leftJoin('c.images', 'i')
        ->addSelect('i')
        ->getQuery()
        ->getResult();

    return $this->render('page/index.html.twig', [
        'categories' => $categories
    ]);
}


    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig');
    }

 
}
