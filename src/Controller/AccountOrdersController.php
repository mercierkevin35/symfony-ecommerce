<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;
use App\Entity\Product;

class AccountOrdersController extends AbstractController
{
    /**
     * @Route("/account/orders", name="account_orders")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Order::class);
        $orders = $repository->findAllPaidByUser($this->getUser());
        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("account/orders/show/{reference}", name="account_orders_show")
     */
    public function show($reference){
        $orderRepository = $this->getDoctrine()->getRepository(Order::class);
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $order = $orderRepository->findOneByReference($reference);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
        $orderDetails = $order->getOrderDetails()->getValues();
        foreach($orderDetails as $detail){
            if($product = $productRepository->findOneById($detail->getProductId())){
                $detail->slug = $product->getSlug();
            }
        }
        return $this->render('account/show_order.html.twig', [
            'order' => $order,
            'orderDetails' => $orderDetails
        ]);
    }
}
