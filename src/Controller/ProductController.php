<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
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
        //****************Manage Uploaded FileName
        $photo_prod = $form->get('image')->getData();
        $originalFilename = $photo_prod->getClientOriginalName();
        $newFilename = $originalFilename.'-'.uniqid().'.'.$photo_prod->getClientOriginalExtension();
        $photo_prod->move($this->getParameter('images_directory'),$newFilename);
        $product->setPhoto($newFilename);
        //****************
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();
        //return $this->redirectToRoute('confirm');
        }
        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    
    public function new(Request $request, ProductRepository $productRepository, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if file upload fails
                }
    
                $product->setImage($newFilename);
            }
    
            $productRepository->save($product, true);
    
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $produit): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $produit,
        ]);
    }

   



    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $produit, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $productRepository->remove($produit, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
