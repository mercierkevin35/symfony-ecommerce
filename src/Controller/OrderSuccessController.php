<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Classe\Cart;

class OrderSuccessController extends AbstractController
{
    /**
     * @Route("/order/success/{stripe_session_id}", name="order_success")
     */
    public function index(Cart $cart, $stripe_session_id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Order::class);
        $order = $repository->findOneByStripeSessionId($stripe_session_id);
        
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if($order->getState() == $order::UNPAID){
            $cart->clear();
            $order->setState($order::PAID);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
