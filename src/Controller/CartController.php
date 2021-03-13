<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Classe\Cart;
use App\Entity\Product;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart->get()
        ]);
    }

    /**
     * @Route("/cart/increase/{id<\d+>}", name="add_item")
     */
    public function increase(Cart $cart, int $id): Response
    {
        $cart->increase($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decrease/{id<\d+>}", name="remove_item")
     */
    public function decrease(Cart $cart, int $id): Response
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/clearall", name="clear_cart")
     */
    public function clearAll(Cart $cart): Response
    {
        $cart->clear();

        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/clear/{id<\d+>}", name="clear_item")
     */
    public function clear(Cart $cart, int $id): Response
    {
        $cart->clear($id);

        return $this->redirectToRoute('cart');
    }
}
