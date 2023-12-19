<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class StoreController extends AbstractController
{
    #[Route('/store', name: 'app_store')]
    public function index(Request $request ,ProductRepository $productRepository)
    {
        $searchQuery = $request->query->get('search');
        if ($searchQuery) {
            $products = $productRepository->findBySearchQuery($searchQuery);
        } else {
            $products = $productRepository->findAll();
        }
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $produit = $form->getData();
        //**Manage Uploaded FileName
        $photo_prod = $form->get('image')->getData();
        $originalFilename = $photo_prod->getClientOriginalName();
        $newFilename = $originalFilename.'-'.uniqid().'.'.$photo_prod->getClientOriginalExtension();
        $photo_prod->move($this->getParameter('images_directory'),$newFilename);
        $product->setPhoto($newFilename);
        //**
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
        //return $this->redirectToRoute('confirm');
        }
        return $this->render('store/index.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/{id}', name: 'app_store_show', methods: ['GET'])]
    public function show(Product $produit): Response
    {
        return $this->render('store/show.html.twig', [
            'product' => $produit,
        ]);
    }




}
