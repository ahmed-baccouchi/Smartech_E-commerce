<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
{
   
    $cartItems = $request->getSession()->get('cart_items', []);

    
    $cart = [];

    
    foreach ($cartItems as $itemId) {
        if (!isset($cart[$itemId])) {
            $cart[$itemId] = 0;
        }
        $cart[$itemId]++;
    }

    $totalPrice = 0;
    $entityManager = $this->getDoctrine()->getManager();
    $articles = $entityManager->getRepository(Product::class)->findBy(['id' => array_keys($cart)]);

    
    foreach ($articles as $article) {
        $totalPrice += $article->getPrix() * $cart[$article->getId()];
    }

    return $this->render('cart/index.html.twig', ['articles' => $articles, 'cart' => $cart, 'totalPrice' => $totalPrice]);
}

    /**
     * @Route("/add/{id}", name="app_add")
     */
    public function add($id, Request $request)
    {
       
        $cartItems = $request->getSession()->get('cart_items', []);

        
        $cartItems[] = $id;

       
        $request->getSession()->set('cart_items', $cartItems);

        
        return $this->redirectToRoute('app_cart');
    }
    /**
     * @Route("/add2/{id}", name="app2_add")
     */
    public function add2($id, Request $request)
    {
        
        $cartItems = $request->getSession()->get('cart_items', []);

        
        $cartItems[] = $id;

       
        $request->getSession()->set('cart_items', $cartItems);

       
        return $this->redirectToRoute('app_store');
    }
    /**
 * @Route("/delete/{id}", name="app_delete")
 */
    public function delete($id, Request $request)
{
   
    $cartItems = $request->getSession()->get('cart_items', []);

  
    $index = array_search($id, $cartItems);

    
    if ($index !== false) {
        unset($cartItems[$index]);
    }

  
    $request->getSession()->set('cart_items', $cartItems);

   
    return $this->redirectToRoute('app_cart');
}
}