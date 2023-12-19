<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
    $articles = $this->getDoctrine()->getRepository(Product::class)->findBy([/*'id'=>'1'*/]);
    return $this->render('index/index.html.twig', ['articles' => $articles,]);
    }
}
