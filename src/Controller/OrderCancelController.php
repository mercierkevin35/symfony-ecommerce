<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;

class OrderCancelController extends AbstractController
{
    /**
     * @Route("/order/cancel/{stripe_session_id}", name="order_cancel")
     */
    public function index($stripe_session_id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Order::class);
        $order = $repository->findOneByStripeSessionId($stripe_session_id);
        
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        return $this->render('order_cancel/index.html.twig', [
        ]);
    }
}
