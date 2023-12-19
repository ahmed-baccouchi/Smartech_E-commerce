<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Form\ProductType;

class EditController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request, Product $produit): Response
    {
        $form = $this->createForm(ProductType::class, $produit, [
            'photo_value' => $produit->getImage(),
        ]);
   
        
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
       {
            $produit = $form->getData();
                        // Check if the photo field has a value
                        $photo = $form->get('image')->getData();
                        if ($photo) {
                            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                            $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->getClientOriginalExtension();
                            $photo->move(
                                $this->getParameter('images_directory'),
                                $newFilename
                            );
                            $produit->setImage($newFilename);
                        }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }
}